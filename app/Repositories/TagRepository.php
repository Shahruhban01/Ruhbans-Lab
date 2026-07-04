<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class TagRepository extends BaseRepository
{
    protected string $table = 'tags';

    public function allTags(): array
    {
        $statement = $this->connection->query('SELECT id, name, slug FROM tags WHERE deleted_at IS NULL ORDER BY name ASC, id ASC');

        return $statement ? $statement->fetchAll(PDO::FETCH_ASSOC) : array();
    }

    public function findBySlug(string $slug)
    {
        $statement = $this->connection->prepare('SELECT id, name, slug FROM tags WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
        $statement->execute(array('slug' => trim($slug)));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function saveTag(array $data, $id = null): string
    {
        $payload = array(
            'name' => trim((string) ($data['name'] ?? '')),
            'slug' => trim((string) ($data['slug'] ?? '')),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($payload['slug'] === '') {
            $payload['slug'] = slugify($payload['name']);
        }

        $id = $id !== null && $id !== '' ? (int) $id : null;

        if ($id !== null) {
            $statement = $this->connection->prepare('UPDATE tags SET name = :name, slug = :slug, updated_at = :updated_at WHERE id = :id');
            $payload['id'] = $id;
            $statement->execute($payload);

            return (string) $id;
        }

        $statement = $this->connection->prepare('INSERT INTO tags (name, slug, created_at, updated_at) VALUES (:name, :slug, :created_at, :updated_at)');
        $payload['created_at'] = date('Y-m-d H:i:s');
        $statement->execute($payload);

        return (string) $this->connection->lastInsertId();
    }
}
