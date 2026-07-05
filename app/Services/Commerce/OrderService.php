<?php

declare(strict_types=1);

namespace App\Services\Commerce;

use App\Core\Application;
use PDO;

final class OrderService
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function createOrder(int $userId, array $itemIds, ?int $couponId = null): array
    {
        $db = $this->app->database()->connection();
        $db->beginTransaction();

        try {
            $totalCents = 0;
            $items = array();

            foreach ($itemIds as $itemId) {
                $stmt = $db->prepare('SELECT * FROM post_meta WHERE post_id = :post_id LIMIT 1');
                $stmt->execute(array('post_id' => $itemId));
                $meta = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $price = (int) ($meta['price_cents'] ?? 1900); // Default $19.00
                $totalCents += $price;
                $items[] = array('post_id' => $itemId, 'price' => $price);
            }

            if ($couponId) {
                $stmt = $db->prepare('SELECT * FROM coupons WHERE id = :id LIMIT 1');
                $stmt->execute(array('id' => $couponId));
                $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($coupon) {
                    $discountPct = (int) $coupon['discount_percentage'];
                    $discountAmt = (int) round(($totalCents * $discountPct) / 100);
                    $totalCents = max(0, $totalCents - $discountAmt);
                }
            }

            $now = date('Y-m-d H:i:s');
            $stmt = $db->prepare('INSERT INTO orders (user_id, coupon_id, total_amount, status, created_at, updated_at) VALUES (:uid, :cid, :total, "pending", :created, :updated)');
            $stmt->execute(array(
                'uid' => $userId,
                'cid' => $couponId,
                'total' => $totalCents,
                'created' => $now,
                'updated' => $now,
            ));

            $orderId = (int) $db->lastInsertId();

            foreach ($items as $item) {
                $stmt = $db->prepare('INSERT INTO order_items (order_id, post_id, price_cents) VALUES (:oid, :pid, :price)');
                $stmt->execute(array(
                    'oid' => $orderId,
                    'pid' => $item['post_id'],
                    'price' => $item['price'],
                ));
            }

            $db->commit();
            return array('order_id' => $orderId, 'total_amount' => $totalCents);
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function completeOrder(int $orderId, string $transactionRef, string $provider): bool
    {
        $db = $this->app->database()->connection();
        $db->beginTransaction();

        try {
            // Fetch order
            $stmt = $db->prepare('SELECT * FROM orders WHERE id = :id FOR UPDATE');
            $stmt->execute(array('id' => $orderId));
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order || $order['status'] === 'paid') {
                $db->rollBack();
                return false;
            }

            // Update order status
            $now = date('Y-m-d H:i:s');
            $stmt = $db->prepare('UPDATE orders SET status = "paid", updated_at = :updated WHERE id = :id');
            $stmt->execute(array('id' => $orderId, 'updated' => $now));

            // Log Transaction
            $stmt = $db->prepare('INSERT INTO transactions (order_id, provider, transaction_reference, status, amount, created_at) VALUES (:oid, :provider, :ref, "success", :amount, :created)');
            $stmt->execute(array(
                'oid' => $orderId,
                'provider' => $provider,
                'ref' => $transactionRef,
                'amount' => $order['total_amount'],
                'created' => $now,
            ));

            // Register purchases and generate keys
            $stmt = $db->prepare('SELECT * FROM order_items WHERE order_id = :oid');
            $stmt->execute(array('oid' => $orderId));
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                $stmt = $db->prepare('INSERT INTO purchases (user_id, post_id, order_id, purchase_type, created_at) VALUES (:uid, :pid, :oid, "one_time", :created)');
                $stmt->execute(array(
                    'uid' => $order['user_id'],
                    'pid' => $item['post_id'],
                    'oid' => $orderId,
                    'created' => $now,
                ));

                $purchaseId = (int) $db->lastInsertId();

                // License key generation
                $licenseKey = strtoupper('KEY-' . bin2hex(random_bytes(6)) . '-' . bin2hex(random_bytes(6)) . '-' . bin2hex(random_bytes(6)));
                $stmt = $db->prepare('INSERT INTO licenses (purchase_id, license_key, status, expires_at, created_at) VALUES (:pur_id, :key, "active", NULL, :created)');
                $stmt->execute(array(
                    'pur_id' => $purchaseId,
                    'key' => $licenseKey,
                    'created' => $now,
                ));
            }

            $db->commit();
            return true;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
