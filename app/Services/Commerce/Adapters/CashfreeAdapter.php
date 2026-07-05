<?php

declare(strict_types=1);

namespace App\Services\Commerce\Adapters;

use App\Services\Commerce\Contracts\PaymentGatewayInterface;

final class CashfreeAdapter implements PaymentGatewayInterface
{
    public function createOrder(array $params): array
    {
        return array(
            'cf_order_id' => 'cf_' . bin2hex(random_bytes(8)),
            'order_token' => 'cft_' . bin2hex(random_bytes(16)),
            'payment_link' => 'https://checkout.cashfree.com/simulated',
        );
    }

    public function verifyPayment(array $payload): bool
    {
        return !empty($payload['cf_payment_id']);
    }

    public function refundPayment(string $transactionReference): bool
    {
        return true;
    }

    public function getProviderName(): string
    {
        return 'cashfree';
    }
}
