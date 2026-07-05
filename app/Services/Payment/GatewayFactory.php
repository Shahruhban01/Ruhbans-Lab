<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;

final class GatewayFactory
{
    public function make(string $provider): PaymentGatewayInterface
    {
        switch (strtolower(trim($provider))) {
            case 'razorpay':
                return new RazorpayGatewayService();
            case 'stripe':
                return new StripeGatewayService();
            case 'cashfree':
                return new CashfreeGatewayService();
            case 'paypal':
                return new PaypalGatewayService();
            default:
                throw new \InvalidArgumentException('Unsupported payment provider: ' . $provider);
        }
    }
}
