<?php

declare(strict_types=1);

namespace App\Services\Commerce\Adapters;

use App\Services\Commerce\Contracts\PaymentGatewayInterface;

final class PaypalAdapter implements PaymentGatewayInterface
{
    public function createOrder(array $params): array
    {
        return array(
            'id' => 'paypal_order_' . bin2hex(random_bytes(10)),
            'status' => 'CREATED',
            'approve_url' => 'https://www.paypal.com/checkoutnow?token=simulated',
        );
    }

    public function verifyPayment(array $payload): bool
    {
        return !empty($payload['paypal_payment_id']);
    }

    public function refundPayment(string $transactionReference): bool
    {
        return true;
    }

    public function getProviderName(): string
    {
        return 'paypal';
    }
}
