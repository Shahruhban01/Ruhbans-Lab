<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class CommerceRepository extends BaseRepository
{
    public function findOrdersByUserId(int $userId): array
    {
        $statement = $this->connection->prepare('
            SELECT o.*, c.code AS coupon_code, c.discount_percentage
            FROM orders o
            LEFT JOIN coupons c ON c.id = o.coupon_id
            WHERE o.user_id = :user_id
            ORDER BY o.created_at DESC
        ');
        $statement->execute(array('user_id' => $userId));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findOrderItems(int $orderId): array
    {
        $statement = $this->connection->prepare('
            SELECT oi.*, p.title, p.slug
            FROM order_items oi
            LEFT JOIN posts p ON p.id = oi.post_id
            WHERE oi.order_id = :order_id
        ');
        $statement->execute(array('order_id' => $orderId));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findPurchasesByUserId(int $userId): array
    {
        $statement = $this->connection->prepare('
            SELECT pur.*, p.title, p.slug, p.featured_image, o.total_amount, o.status AS order_status
            FROM purchases pur
            LEFT JOIN posts p ON p.id = pur.post_id
            LEFT JOIN orders o ON o.id = pur.order_id
            WHERE pur.user_id = :user_id
            ORDER BY pur.created_at DESC
        ');
        $statement->execute(array('user_id' => $userId));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findLicensesByUserId(int $userId): array
    {
        $statement = $this->connection->prepare('
            SELECT l.*, p.title AS product_name, p.slug AS product_slug
            FROM licenses l
            INNER JOIN purchases pur ON pur.id = l.purchase_id
            INNER JOIN posts p ON p.id = pur.post_id
            WHERE pur.user_id = :user_id
            ORDER BY l.created_at DESC
        ');
        $statement->execute(array('user_id' => $userId));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findTransactionsByUserId(int $userId): array
    {
        $statement = $this->connection->prepare('
            SELECT t.*, o.total_amount
            FROM transactions t
            INNER JOIN orders o ON o.id = t.order_id
            WHERE o.user_id = :user_id
            ORDER BY t.created_at DESC
        ');
        $statement->execute(array('user_id' => $userId));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findCouponByCode(string $code): ?array
    {
        $statement = $this->connection->prepare('
            SELECT * FROM coupons 
            WHERE code = :code AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW())
            LIMIT 1
        ');
        $statement->execute(array('code' => $code));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
