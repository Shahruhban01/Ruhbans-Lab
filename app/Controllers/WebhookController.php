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
        $payload = (array) json_decode((string) $request->body(), true);
        $provider = strtolower(trim($provider));

        // Simulated webhook router
        if ($provider === 'razorpay') {
            if (($payload['event'] ?? '') === 'payment.captured') {
                $orderId = (int) ($payload['payload']['payment']['entity']['notes']['order_id'] ?? 0);
                $transactionRef = (string) ($payload['payload']['payment']['entity']['id'] ?? 'sim_rzp_ref');
                if ($orderId > 0) {
                    $this->orderService->completeOrder($orderId, $transactionRef, 'razorpay');
                }
            }
        } elseif ($provider === 'stripe') {
            if (($payload['type'] ?? '') === 'checkout.session.completed') {
                $orderId = (int) ($payload['data']['object']['metadata']['order_id'] ?? 0);
                $transactionRef = (string) ($payload['data']['object']['payment_intent'] ?? 'sim_stripe_ref');
                if ($orderId > 0) {
                    $this->orderService->completeOrder($orderId, $transactionRef, 'stripe');
                }
            }
        }

        return new Response(json_encode(array('status' => 'received')), 200, array('Content-Type' => 'application/json'));
    }
}
