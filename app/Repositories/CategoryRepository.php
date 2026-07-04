<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class CategoryRepository extends BaseRepository
{
    protected string $table = 'categories';

    public function allCategories(): array
    {
        $statement = $this->connection->query('SELECT id, parent_id, name, slug, description, icon, featured_image FROM categories WHERE deleted_at IS NULL ORDER BY name ASC, id ASC');

        return $statement ? $statement->fetchAll(PDO::FETCH_ASSOC) : array();
    }

    public function findBySlug(string $slug)
    {
        $statement = $this->connection->prepare('SELECT id, parent_id, name, slug, description, icon, featured_image FROM categories WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
        $statement->execute(array('slug' => trim($slug)));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function tree(): array
    {
        $categories = $this->allCategories();
        $indexed = array();

        foreach ($categories as $category) {
            $category['children'] = array();
            $indexed[$category['id']] = $category;
        }

        $tree = array();

        foreach ($indexed as $id => $category) {
            if (!empty($category['parent_id']) && isset($indexed[$category['parent_id']])) {
                $indexed[$category['parent_id']]['children'][] = &$indexed[$id];
                continue;
            }

            $tree[] = &$indexed[$id];
        }

        return $tree;
    }

    public function saveCategory(array $data, $id = null): string
    {
        $payload = array(
            'parent_id' => !empty($data['parent_id']) ? max(1, (int) $data['parent_id']) : null,
            'name' => trim((string) ($data['name'] ?? '')),
            'slug' => trim((string) ($data['slug'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'icon' => trim((string) ($data['icon'] ?? '')),
            'featured_image' => trim((string) ($data['featured_image'] ?? '')),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($payload['slug'] === '') {
            $payload['slug'] = slugify($payload['name']);
        }

        $id = $id !== null && $id !== '' ? (int) $id : null;

        if ($id !== null) {
            $statement = $this->connection->prepare('UPDATE categories SET parent_id = :parent_id, name = :name, slug = :slug, description = :description, icon = :icon, featured_image = :featured_image, updated_at = :updated_at WHERE id = :id');
            $payload['id'] = $id;
            $statement->execute($payload);

            return (string) $id;
        }

        $statement = $this->connection->prepare('INSERT INTO categories (parent_id, name, slug, description, icon, featured_image, created_at, updated_at) VALUES (:parent_id, :name, :slug, :description, :icon, :featured_image, :created_at, :updated_at)');
        $payload['created_at'] = date('Y-m-d H:i:s');
        $statement->execute($payload);

        return (string) $this->connection->lastInsertId();
    }
}
