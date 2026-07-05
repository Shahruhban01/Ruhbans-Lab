<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Core\Application;
use App\Services\Payment\Enums\PaymentStatus;
use PDO;

final class PaymentService
{
    private Application $app;
    private PaymentManager $manager;
    private PaymentLogger $logger;
    private GatewayConfigResolver $configResolver;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->manager = new PaymentManager($app);
        $this->logger = new PaymentLogger($app);
        $this->configResolver = new GatewayConfigResolver($app);
    }

    public function initializePaymentForOrder(int $orderId, string $provider): array
    {
        $db = $this->app->database()->connection();
        
        // Fetch order
        $stmt = $db->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
        $stmt->execute(array('id' => $orderId));
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new \InvalidArgumentException('Order not found: ' . $orderId);
        }

        $gateway = $this->manager->getGateway($provider);
        
        // Load active configuration settings and pass down
        $configData = $this->configResolver->resolve($provider);
        if (method_exists($gateway, 'setConfig')) {
            $gateway->setConfig($configData);
        }

        $initParams = array(
            'amount' => $order['total_amount'],
            'currency' => 'USD',
            'order_id' => $orderId,
        );

        $this->logger->log('outbound', array('action' => 'initialize', 'params' => $initParams));
        $response = $gateway->initializePayment($initParams);

        // Record locally in payment_transactions
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare('
            INSERT INTO payment_transactions (order_id, amount, currency, gateway, gateway_transaction_id, status, created_at, updated_at)
            VALUES (:oid, :amount, :currency, :gateway, :gate_tx, :status, :created, :updated)
        ');
        $stmt->execute(array(
            'oid' => $orderId,
            'amount' => $order['total_amount'],
            'currency' => 'USD',
            'gateway' => $provider,
            'gate_tx' => $response['gateway_order_id'] ?? $response['cf_order_id'] ?? $response['paypal_order_id'] ?? null,
            'status' => PaymentStatus::PENDING,
            'created' => $now,
            'updated' => $now,
        ));
        $txId = (int) $db->lastInsertId();

        $this->logger->log('inbound', $response, $txId);

        return array(
            'transaction_id' => $txId,
            'gateway_payload' => $response,
        );
    }

    public function verifyPaymentTransaction(int $transactionId, array $payload): bool
    {
        $db = $this->app->database()->connection();
        $stmt = $db->prepare('SELECT * FROM payment_transactions WHERE id = :id LIMIT 1');
        $stmt->execute(array('id' => $transactionId));
        $tx = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tx) {
            return false;
        }

        $gateway = $this->manager->getGateway($tx['gateway']);
        
        // Load active configuration settings and pass down
        $configData = $this->configResolver->resolve($tx['gateway']);
        if (method_exists($gateway, 'setConfig')) {
            $gateway->setConfig($configData);
        }

        $this->logger->log('outbound', array('action' => 'verify', 'payload' => $payload), $transactionId);
        $success = $gateway->verifyPayment($payload);

        $now = date('Y-m-d H:i:s');
        $status = $success ? PaymentStatus::SUCCESS : PaymentStatus::FAILED;

        $stmt = $db->prepare('UPDATE payment_transactions SET status = :status, updated_at = :updated WHERE id = :id');
        $stmt->execute(array('id' => $transactionId, 'status' => $status, 'updated' => $now));

        $this->logger->log('inbound', array('verified' => $success, 'status' => $status), $transactionId);

        return $success;
    }
}
