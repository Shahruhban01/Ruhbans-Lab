<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Core\Application;

final class PaymentLogger
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function log(string $direction, array $payload, ?int $transactionId = null): void
    {
        try {
            $db = $this->app->database()->connection();
            $stmt = $db->prepare('INSERT INTO gateway_logs (transaction_id, direction, payload_json, created_at) VALUES (:tx_id, :direction, :payload, :created)');
            $stmt->execute(array(
                'tx_id' => $transactionId,
                'direction' => $direction,
                'payload' => json_encode($payload),
                'created' => date('Y-m-d H:i:s'),
            ));
        } catch (\Throwable $e) {
            // Silently capture log exceptions to prevent transaction failures
            error_log('PaymentLogger failed: ' . $e->getMessage());
        }
    }
}
