<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class RoleRepository extends BaseRepository
{
    protected string $table = 'roles';

    public function allRoles(): array
    {
        $statement = $this->connection->query('SELECT id, name, slug FROM roles ORDER BY id ASC');

        return $statement ? $statement->fetchAll() : array();
    }

    public function findBySlug(string $slug)
    {
        $statement = $this->connection->prepare('SELECT id, name, slug FROM roles WHERE slug = :slug LIMIT 1');
        $statement->execute(array('slug' => $slug));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}
