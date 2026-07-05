<?php

declare(strict_types=1);

namespace App\Services\Commerce\Adapters;

use App\Services\Commerce\Contracts\PaymentGatewayInterface;

final class StripeAdapter implements PaymentGatewayInterface
{
    public function createOrder(array $params): array
    {
        return array(
            'id' => 'ch_' . bin2hex(random_bytes(12)),
            'client_secret' => 'pi_' . bin2hex(random_bytes(16)) . '_secret_stripe',
            'amount' => $params['amount'] ?? 0,
            'status' => 'requires_payment_method',
        );
    }

    public function verifyPayment(array $payload): bool
    {
        return !empty($payload['stripe_session_id']);
    }

    public function refundPayment(string $transactionReference): bool
    {
        return true;
    }

    public function getProviderName(): string
    {
        return 'stripe';
    }
}
