<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class AuditLogRepository extends BaseRepository
{
    protected string $table = 'audit_logs';

    public function recent(int $limit = 10): array
    {
        $statement = $this->connection->prepare('SELECT a.*, u.name AS user_name, u.email AS user_email FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC LIMIT :limit');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countToday(): int
    {
        $statement = $this->connection->query('SELECT COUNT(*) FROM audit_logs WHERE DATE(created_at) = CURRENT_DATE');

        return (int) $statement->fetchColumn();
    }

    public function createLog($userId, string $action, string $description, array $context = array(), string $ipAddress = '', string $userAgent = ''): bool
    {
        $statement = $this->connection->prepare('INSERT INTO audit_logs (user_id, action, description, context, ip_address, user_agent, created_at) VALUES (:user_id, :action, :description, :context, :ip_address, :user_agent, :created_at)');

        return $statement->execute(array(
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'context' => json_encode($context),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => date('Y-m-d H:i:s'),
        ));
    }
}
