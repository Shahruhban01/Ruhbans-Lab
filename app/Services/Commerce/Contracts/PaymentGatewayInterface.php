<?php

declare(strict_types=1);

namespace App\Services\Commerce\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Create a new payment session or transaction order with the provider.
     */
    public function createOrder(array $params): array;

    /**
     * Verify payment status payload returned by checkout options.
     */
    public function verifyPayment(array $payload): bool;

    /**
     * Process simulated refunds on transaction references.
     */
    public function refundPayment(string $transactionReference): bool;

    /**
     * Get unique slug indicator for provider.
     */
    public function getProviderName(): string;
}
