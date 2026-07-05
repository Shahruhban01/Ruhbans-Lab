<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\Payment\PaymentService;
use App\Services\Commerce\OrderService;
use PDO;

final class RazorpayController extends BaseController
{
    private PaymentService $paymentService;
    private OrderService $orderService;

    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);
        $this->paymentService = new PaymentService($this->app);
        $this->orderService = new OrderService($this->app);
    }

    public function initialize(Request $request)
    {
        $currentUser = $this->currentUser();
        if (!$currentUser) {
            return new Response(json_encode(array('error' => 'Login required')), 401, array('Content-Type' => 'application/json'));
        }

        $planId = (int) $request->input('plan_id', 0);
        $productId = (int) $request->input('product_id', 0);

        $db = $this->app->database()->connection();

        // 1. Create order representation
        $totalCents = 1900; // Default fallback
        $itemIds = array();

        if ($planId > 0) {
            $stmt = $db->prepare('SELECT * FROM membership_plans WHERE id = :id LIMIT 1');
            $stmt->execute(array('id' => $planId));
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($plan) {
                $totalCents = (int) $plan['price_cents'];
            }
        } elseif ($productId > 0) {
            $itemIds[] = $productId;
            $stmt = $db->prepare('SELECT * FROM post_meta WHERE post_id = :post_id LIMIT 1');
            $stmt->execute(array('post_id' => $productId));
            $meta = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($meta) {
                $totalCents = (int) ($meta['price_cents'] ?? 1900);
            }
        }

        // Generate temporary mock order
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare('INSERT INTO orders (user_id, total_amount, status, created_at, updated_at) VALUES (:uid, :total, "pending", :created, :updated)');
        $stmt->execute(array(
            'uid' => (int) $currentUser['id'],
            'total' => $totalCents,
            'created' => $now,
            'updated' => $now,
        ));
        $orderId = (int) $db->lastInsertId();

        // Add order items if product
        foreach ($itemIds as $pid) {
            $stmt = $db->prepare('INSERT INTO order_items (order_id, post_id, price_cents) VALUES (:oid, :pid, :price)');
            $stmt->execute(array(
                'oid' => $orderId,
                'pid' => $pid,
                'price' => $totalCents,
            ));
        }

        // 2. Initialize Payment Gateway transaction
        $initResult = $this->paymentService->initializePaymentForOrder($orderId, 'razorpay');

        // Resolve dynamic database config settings
        $configResolver = new \App\Services\Payment\GatewayConfigResolver($this->app);
        $configData = $configResolver->resolve('razorpay');
        $keyId = $configData['config']['key_id'] ?? 'rzp_test_key_placeholder';
        $merchantName = $configData['config']['merchant_name'] ?? 'Developer Ruhban';
        $themeColor = $configData['config']['theme_color'] ?? '#6366f1';

        // Append public configuration settings
        $gatewayPayload = $initResult['gateway_payload'];
        $gatewayPayload['key'] = $keyId;
        $gatewayPayload['name'] = $merchantName;
        $gatewayPayload['order_id'] = $gatewayPayload['gateway_order_id'];
        $gatewayPayload['prefill'] = array(
            'name' => $currentUser['name'] ?? '',
            'email' => $currentUser['email'] ?? '',
        );
        $gatewayPayload['notes'] = array(
            'order_id' => $orderId,
            'plan_id' => $planId,
            'product_id' => $productId,
            'transaction_id' => $initResult['transaction_id'],
        );

        return new Response(json_encode($gatewayPayload), 200, array('Content-Type' => 'application/json'));
    }

    public function verify(Request $request)
    {
        $payload = (array) json_decode((string) $request->body(), true);

        $txId = (int) ($payload['transaction_id'] ?? 0);
        $orderId = (int) ($payload['order_id'] ?? 0);
        $planId = (int) ($payload['plan_id'] ?? 0);
        $productId = (int) ($payload['product_id'] ?? 0);

        if ($txId <= 0 || $orderId <= 0) {
            return new Response(json_encode(array('success' => false, 'error' => 'Invalid transaction params')), 400, array('Content-Type' => 'application/json'));
        }

        // Verify gateway signature
        $verified = $this->paymentService->verifyPaymentTransaction($txId, $payload);

        if (!$verified) {
            return new Response(json_encode(array('success' => false, 'error' => 'Signature verification failed')), 400, array('Content-Type' => 'application/json'));
        }

        // Transaction successful -> Complete Order updates and apply memberships
        $transactionRef = (string) ($payload['razorpay_payment_id'] ?? 'sim_rzp_payment');
        $this->orderService->completeOrder($orderId, $transactionRef, 'razorpay');

        // Apply memberships specifically if plan_id exists
        if ($planId > 0) {
            $currentUser = $this->currentUser();
            if ($currentUser) {
                $membershipRepository = new \App\Repositories\MembershipRepository($this->app->database()->connection());
                $membershipRepository->assignPlan((int) $currentUser['id'], $planId, null);
                
                // Fetch active plan details to get name
                $stmtPlan = $this->app->database()->connection()->prepare('SELECT plan_name FROM user_memberships WHERE user_id = :uid ORDER BY id DESC LIMIT 1');
                $stmtPlan->execute(array('uid' => $currentUser['id']));
                $planName = $stmtPlan->fetchColumn() ?: 'Premium';

                // Automated Notification creation
                $engagementRepo = new \App\Repositories\EngagementRepository($this->app->database()->connection());
                $engagementRepo->createNotification(
                    (int) $currentUser['id'],
                    'membership_activated',
                    'Membership Activated!',
                    'Your account has successfully upgraded to the ' . $planName . ' plan tier.',
                    url('/account/membership')
                );

                // Update session variables
                $userRepo = new \App\Repositories\UserRepository($this->app->database()->connection());
                $refreshed = $userRepo->findWithRole((int) $currentUser['id']);
                if ($refreshed) {
                    $this->app->session()->set(
                        (string) $this->app->config()->get('auth.session_key', 'auth_user'),
                        $refreshed
                    );
                }
            }
        } elseif ($productId > 0) {
            $currentUser = $this->currentUser();
            if ($currentUser) {
                // Fetch product details
                $stmtProd = $this->app->database()->connection()->prepare('SELECT title FROM posts WHERE id = :id LIMIT 1');
                $stmtProd->execute(array('id' => $productId));
                $pTitle = $stmtProd->fetchColumn() ?: 'Product';

                // Automated Notification creation
                $engagementRepo = new \App\Repositories\EngagementRepository($this->app->database()->connection());
                $engagementRepo->createNotification(
                    (int) $currentUser['id'],
                    'purchase_success',
                    'Product Unlocked!',
                    'You have successfully unlocked access to ' . $pTitle . '. View your license keys.',
                    url('/account/licenses')
                );
            }
        }

        return new Response(json_encode(array('success' => true)), 200, array('Content-Type' => 'application/json'));
    }
}
