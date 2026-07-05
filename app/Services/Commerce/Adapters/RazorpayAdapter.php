<?php

declare(strict_types=1);

namespace App\Services\Commerce\Adapters;

use App\Services\Commerce\Contracts\PaymentGatewayInterface;

final class RazorpayAdapter implements PaymentGatewayInterface
{
    public function createOrder(array $params): array
    {
        return array(
            'id' => 'rzp_order_' . bin2hex(random_bytes(8)),
            'amount' => $params['amount'] ?? 0,
            'currency' => 'USD',
            'status' => 'created',
        );
    }

    public function verifyPayment(array $payload): bool
    {
        return !empty($payload['razorpay_payment_id']);
    }

    public function refundPayment(string $transactionReference): bool
    {
        return true;
    }

    public function getProviderName(): string
    {
        return 'razorpay';
    }
}
