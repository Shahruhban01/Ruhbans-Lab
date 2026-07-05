<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;

final class StripeGatewayService implements PaymentGatewayInterface
{
    public function initializePayment(array $params): array
    {
        return array(
            'client_secret' => 'pi_' . bin2hex(random_bytes(10)) . '_secret_simulated',
            'amount' => $params['amount'] ?? 0,
            'currency' => $params['currency'] ?? 'USD',
            'status' => 'initialized',
        );
    }

    public function verifyPayment(array $payload): bool
    {
        return !empty($payload['stripe_payment_intent']);
    }

    public function processRefund(string $transactionReference, int $amountCents): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'stripe';
    }
}
