<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function findByEmail(string $email)
    {
        $statement = $this->connection->prepare('SELECT u.*, r.name AS role_name, r.slug AS role_slug FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.email = :email AND u.deleted_at IS NULL LIMIT 1');
        $statement->execute(array('email' => $email));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function findWithRole($id)
    {
        $statement = $this->connection->prepare('SELECT u.*, r.name AS role_name, r.slug AS role_slug FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.id = :id AND u.deleted_at IS NULL LIMIT 1');
        $statement->execute(array('id' => $id));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername(string $username)
    {
        $statement = $this->connection->prepare('SELECT u.*, r.name AS role_name, r.slug AS role_slug FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.username = :username AND u.deleted_at IS NULL LIMIT 1');
        $statement->execute(array('username' => trim($username)));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function paginateWithRoles(string $search = '', int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $searchLike = '%' . $search . '%';

        $where = 'WHERE u.deleted_at IS NULL';
        $bindings = array();

        if ($search !== '') {
            $where .= ' AND (u.name LIKE :search OR u.email LIKE :search OR u.username LIKE :search OR r.name LIKE :search)';
            $bindings['search'] = $searchLike;
        }

        $countSql = 'SELECT COUNT(*) FROM users u LEFT JOIN roles r ON r.id = u.role_id ' . $where;
        $countStatement = $this->connection->prepare($countSql);
        $countStatement->execute($bindings);
        $total = (int) $countStatement->fetchColumn();

        $sql = 'SELECT u.*, r.name AS role_name, r.slug AS role_slug FROM users u LEFT JOIN roles r ON r.id = u.role_id ' . $where . ' ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset';
        $statement = $this->connection->prepare($sql);

        foreach ($bindings as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }

        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return array(
            'data' => $statement->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => array(
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'pages' => (int) ceil($total / $perPage),
            ),
        );
    }

    public function countActive(): int
    {
        return (int) $this->connection->query('SELECT COUNT(*) FROM users WHERE is_active = 1 AND deleted_at IS NULL')->fetchColumn();
    }

    public function countByRoleSlug(string $roleSlug): int
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE r.slug = :slug AND u.deleted_at IS NULL');
        $statement->execute(array('slug' => $roleSlug));

        return (int) $statement->fetchColumn();
    }

    public function countTotal(): int
    {
        return (int) $this->connection->query('SELECT COUNT(*) FROM users WHERE deleted_at IS NULL')->fetchColumn();
    }

    public function recent(int $limit = 5): array
    {
        $statement = $this->connection->prepare('SELECT u.*, r.name AS role_name, r.slug AS role_slug FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.deleted_at IS NULL ORDER BY u.created_at DESC LIMIT :limit');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateLastLogin($id): bool
    {
        $statement = $this->connection->prepare('UPDATE users SET last_login = :last_login, updated_at = :updated_at WHERE id = :id');

        return $statement->execute(array(
            'last_login' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $id,
        ));
    }

    public function updatePassword($id, string $passwordHash): bool
    {
        $statement = $this->connection->prepare('UPDATE users SET password = :password, updated_at = :updated_at WHERE id = :id');

        return $statement->execute(array(
            'password' => $passwordHash,
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $id,
        ));
    }

    public function updateRole($id, $roleId): bool
    {
        $statement = $this->connection->prepare('UPDATE users SET role_id = :role_id, updated_at = :updated_at WHERE id = :id');

        return $statement->execute(array(
            'role_id' => $roleId,
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $id,
        ));
    }

    public function updateStatus($id, int $isActive): bool
    {
        $statement = $this->connection->prepare('UPDATE users SET is_active = :is_active, updated_at = :updated_at WHERE id = :id');

        return $statement->execute(array(
            'is_active' => $isActive,
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $id,
        ));
    }
}
