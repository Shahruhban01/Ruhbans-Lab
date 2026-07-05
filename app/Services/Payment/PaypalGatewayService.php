<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;

final class PaypalGatewayService implements PaymentGatewayInterface
{
    public function initializePayment(array $params): array
    {
        return array(
            'paypal_order_id' => 'pay_' . bin2hex(random_bytes(8)),
            'amount' => $params['amount'] ?? 0,
            'status' => 'initialized',
        );
    }

    public function verifyPayment(array $payload): bool
    {
        return !empty($payload['paypal_payment_id']);
    }

    public function processRefund(string $transactionReference, int $amountCents): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'paypal';
    }
}
