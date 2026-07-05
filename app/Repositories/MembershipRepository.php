<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class MembershipRepository extends BaseRepository
{
    protected string $table = 'membership_plans';

    public function allPlans(): array
    {
        $statement = $this->connection->query('SELECT * FROM membership_plans ORDER BY id ASC');
        return $statement ? $statement->fetchAll(PDO::FETCH_ASSOC) : array();
    }

    public function findPlanBySlug(string $slug)
    {
        $statement = $this->connection->prepare('SELECT * FROM membership_plans WHERE slug = :slug LIMIT 1');
        $statement->execute(array('slug' => $slug));
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getActiveMembership(int $userId)
    {
        $statement = $this->connection->prepare('
            SELECT um.*, mp.name AS plan_name, mp.slug AS plan_slug, mp.features
            FROM user_memberships um
            INNER JOIN membership_plans mp ON mp.id = um.plan_id
            WHERE um.user_id = :user_id AND um.status = "active"
            AND (um.ends_at IS NULL OR um.ends_at > NOW())
            LIMIT 1
        ');
        $statement->execute(array('user_id' => $userId));
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function assignPlan(int $userId, int $planId, ?string $endsAt = null): bool
    {
        $statement = $this->connection->prepare('
            INSERT INTO user_memberships (user_id, plan_id, status, starts_at, ends_at, created_at, updated_at)
            VALUES (:user_id, :plan_id, "active", NOW(), :ends_at, NOW(), NOW())
            ON DUPLICATE KEY UPDATE plan_id = :plan_id_update, status = "active", starts_at = NOW(), ends_at = :ends_at_update, updated_at = NOW()
        ');
        
        return $statement->execute(array(
            'user_id' => $userId,
            'plan_id' => $planId,
            'plan_id_update' => $planId,
            'ends_at' => $endsAt,
            'ends_at_update' => $endsAt
        ));
    }

    public function cancelMembership(int $userId): bool
    {
        $statement = $this->connection->prepare('UPDATE user_memberships SET status = "cancelled", updated_at = NOW() WHERE user_id = :user_id AND status = "active"');
        return $statement->execute(array('user_id' => $userId));
    }

    public function getMembershipsList(int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $count = (int) $this->connection->query('SELECT COUNT(DISTINCT user_id) FROM user_memberships')->fetchColumn();

        $statement = $this->connection->prepare('
            SELECT um.*, u.name AS user_name, u.email AS user_email, mp.name AS plan_name, mp.slug AS plan_slug
            FROM user_memberships um
            INNER JOIN users u ON u.id = um.user_id
            INNER JOIN membership_plans mp ON mp.id = um.plan_id
            ORDER BY um.created_at DESC
            LIMIT :limit OFFSET :offset
        ');
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return array(
            'data' => $statement->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => array(
                'page' => $page,
                'per_page' => $perPage,
                'total' => $count,
                'pages' => (int) ceil($count / $perPage),
            ),
        );
    }
}
