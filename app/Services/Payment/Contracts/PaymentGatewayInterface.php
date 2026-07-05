<?php

declare(strict_types=1);

namespace App\Services\Payment\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Initialize payment session or transaction payload details.
     */
    public function initializePayment(array $params): array;

    /**
     * Verify payment payload response.
     */
    public function verifyPayment(array $payload): bool;

    /**
     * Process refund on reference transaction.
     */
    public function processRefund(string $transactionReference, int $amountCents): bool;

    /**
     * Get provider name.
     */
    public function getName(): string;
}
