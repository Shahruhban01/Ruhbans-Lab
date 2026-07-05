<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;

final class CashfreeGatewayService implements PaymentGatewayInterface
{
    public function initializePayment(array $params): array
    {
        return array(
            'cf_order_id' => 'cf_' . bin2hex(random_bytes(6)),
            'payment_session_id' => 'session_' . bin2hex(random_bytes(10)),
            'amount' => $params['amount'] ?? 0,
            'status' => 'initialized',
        );
    }

    public function verifyPayment(array $payload): bool
    {
        return !empty($payload['cf_payment_id']);
    }

    public function processRefund(string $transactionReference, int $amountCents): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'cashfree';
    }
}
