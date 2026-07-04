<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class RedirectRepository extends BaseRepository
{
    protected string $table = 'redirects';

    private ?bool $tableExists = null;

    public function findBySourcePath(string $path): ?array
    {
        if (!$this->hasTable()) {
            return null;
        }

        $normalizedPath = $this->normalizePath($path);
        $statement = $this->connection->prepare('SELECT * FROM redirects WHERE source_path = :source_path AND is_active = 1 LIMIT 1');
        $statement->execute(array('source_path' => $normalizedPath));
        $redirect = $statement->fetch(PDO::FETCH_ASSOC);

        return $redirect !== false ? $redirect : null;
    }

    public function allActive(): array
    {
        if (!$this->hasTable()) {
            return array();
        }

        $statement = $this->connection->query('SELECT * FROM redirects WHERE is_active = 1 ORDER BY created_at DESC');

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allRedirects(): array
    {
        if (!$this->hasTable()) {
            return array();
        }

        $statement = $this->connection->query('SELECT * FROM redirects ORDER BY is_active DESC, created_at DESC');

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveRedirect(array $data, $id = null): string
    {
        if (!$this->hasTable()) {
            throw new \RuntimeException('Redirects table is missing.');
        }

        $payload = array(
            'source_path' => $this->normalizePath((string) ($data['source_path'] ?? '')),
            'target_path' => trim((string) ($data['target_path'] ?? '')),
            'status_code' => in_array((int) ($data['status_code'] ?? 301), array(301, 302, 410), true) ? (int) $data['status_code'] : 301,
            'reason' => trim((string) ($data['reason'] ?? '')),
            'is_active' => !empty($data['is_active']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($payload['source_path'] === '/' || $payload['source_path'] === '') {
            throw new \InvalidArgumentException('A valid source path is required.');
        }

        if ($payload['target_path'] === '' && (int) $payload['status_code'] !== 410) {
            throw new \InvalidArgumentException('A target path is required for redirects.');
        }

        $id = $id !== null && $id !== '' ? (int) $id : null;

        if ($id !== null) {
            $statement = $this->connection->prepare('UPDATE redirects SET source_path = :source_path, target_path = :target_path, status_code = :status_code, reason = :reason, is_active = :is_active, updated_at = :updated_at WHERE id = :id');
            $payload['id'] = $id;
            $statement->execute($payload);

            return (string) $id;
        }

        $statement = $this->connection->prepare('INSERT INTO redirects (source_path, target_path, status_code, reason, is_active, created_at, updated_at) VALUES (:source_path, :target_path, :status_code, :reason, :is_active, :created_at, :updated_at)');
        $payload['created_at'] = date('Y-m-d H:i:s');
        $statement->execute($payload);

        return (string) $this->connection->lastInsertId();
    }

    public function deleteRedirect($id): bool
    {
        if (!$this->hasTable()) {
            return false;
        }

        $statement = $this->connection->prepare('DELETE FROM redirects WHERE id = :id');

        return $statement->execute(array('id' => (int) $id));
    }

    private function hasTable(): bool
    {
        if ($this->tableExists !== null) {
            return $this->tableExists;
        }

        try {
            $statement = $this->connection->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name LIMIT 1');
            $statement->execute(array('table_name' => 'redirects'));
            $this->tableExists = (bool) $statement->fetchColumn();
        } catch (\Throwable $exception) {
            $this->tableExists = false;
        }

        return $this->tableExists;
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }
}