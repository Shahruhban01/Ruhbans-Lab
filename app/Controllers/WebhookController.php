<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\Commerce\OrderService;

final class WebhookController extends BaseController
{
    private OrderService $orderService;

    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);
        $this->orderService = new OrderService($this->app);
    }

    public function handle(Request $request, string $provider)
    {
        $body = (string) $request->body();
        $payload = (array) json_decode($body, true);
        $provider = strtolower(trim($provider));

        $db = $this->app->database()->connection();
        $now = date('Y-m-d H:i:s');

        // 1. Load active config & signature details
        $configResolver = new \App\Services\Payment\GatewayConfigResolver($this->app);
        $configData = $configResolver->resolve($provider);
        $mode = $configData['mode'] ?? 'test';

        // 2. Validate HMAC token signature
        if ($provider === 'razorpay') {
            $signature = (string) $request->header('X-Razorpay-Signature', '');
            $webhookSecret = $configData['config']['webhook_secret'] ?? 'rzp_test_webhook_secret';
            
            $gateway = new \App\Services\Payment\RazorpayGatewayService();
            $gateway->setConfig($configData);

            if (!$gateway->verifyWebhookSignature($body, $signature, $webhookSecret)) {
                // Log failed event
                $db->prepare('INSERT INTO payment_events (event_name, status, payload_json, created_at) VALUES ("webhook.signature_failed", "failed", :payload, :created)')
                   ->execute(array('payload' => $body, 'created' => $now));
                return new Response(json_encode(array('error' => 'Invalid signature')), 400, array('Content-Type' => 'application/json'));
            }

            // Process payload
            if (($payload['event'] ?? '') === 'payment.captured') {
                $orderId = (int) ($payload['payload']['payment']['entity']['notes']['order_id'] ?? 0);
                $paymentId = (string) ($payload['payload']['payment']['entity']['id'] ?? '');

                if ($orderId > 0 && $paymentId !== '') {
                    // Check duplicate/idempotency protection
                    $stmt = $db->prepare('SELECT status FROM orders WHERE id = :id LIMIT 1');
                    $stmt->execute(array('id' => $orderId));
                    $status = $stmt->fetchColumn();

                    if ($status === 'paid') {
                        return new Response(json_encode(array('status' => 'duplicate')), 200, array('Content-Type' => 'application/json'));
                    }

                    // Complete
                    $this->orderService->completeOrder($orderId, $paymentId, 'razorpay');
                    
                    // Log success event
                    $db->prepare('INSERT INTO payment_events (event_name, status, payload_json, created_at) VALUES ("payment.captured", "success", :payload, :created)')
                       ->execute(array('payload' => $body, 'created' => $now));
                }
            }
        }

        return new Response(json_encode(array('status' => 'received')), 200, array('Content-Type' => 'application/json'));
    }
}
