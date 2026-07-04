<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class AnalyticsRepository extends BaseRepository
{
    protected string $table = 'posts';

    private array $tableCache = array();

    public function summaryCards(): array
    {
        $contentTotal = $this->scalar('SELECT COUNT(*) FROM posts WHERE deleted_at IS NULL');
        $publishedTotal = $this->scalar('SELECT COUNT(*) FROM posts WHERE deleted_at IS NULL AND status = "published"');
        $userTotal = $this->scalar('SELECT COUNT(*) FROM users WHERE deleted_at IS NULL');
        $activeUsers = $this->scalar('SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND is_active = 1');
        $mediaTotal = $this->hasTable('media') ? $this->scalar('SELECT COUNT(*) FROM media WHERE deleted_at IS NULL') : 0;
        $mediaBytes = $this->hasTable('media') ? $this->scalar('SELECT COALESCE(SUM(file_size), 0) FROM media WHERE deleted_at IS NULL') : 0;
        $searchTotal = $this->hasTable('search_queries') ? $this->scalar('SELECT COUNT(*) FROM search_queries') : 0;
        $searchUnique = $this->hasTable('search_queries') ? $this->scalar('SELECT COUNT(DISTINCT normalized_term) FROM search_queries') : 0;

        return array(
            array('label' => 'Content Items', 'value' => $contentTotal, 'note' => 'Published: ' . $publishedTotal),
            array('label' => 'Users', 'value' => $userTotal, 'note' => 'Active: ' . $activeUsers),
            array('label' => 'Media Files', 'value' => $mediaTotal, 'note' => 'Storage: ' . $this->humanFileSize($mediaBytes)),
            array('label' => 'Search Queries', 'value' => $searchTotal, 'note' => 'Unique terms: ' . $searchUnique),
        );
    }

    public function contentStats(): array
    {
        $query = 'SELECT ct.name, ct.slug, COUNT(p.id) AS total, SUM(CASE WHEN p.status = "published" THEN 1 ELSE 0 END) AS published_total FROM content_types ct LEFT JOIN posts p ON p.content_type_id = ct.id AND p.deleted_at IS NULL GROUP BY ct.id, ct.name, ct.slug ORDER BY total DESC, ct.name ASC';
        $statement = $this->connection->query($query);

        return $statement ? $statement->fetchAll(PDO::FETCH_ASSOC) : array();
    }

    public function mediaStats(): array
    {
        if (!$this->hasTable('media')) {
            return array('by_type' => array(), 'recent' => array(), 'totals' => array('count' => 0, 'bytes' => 0));
        }

        $byType = $this->connection->query('SELECT mime_type, COUNT(*) AS total, COALESCE(SUM(file_size), 0) AS bytes FROM media WHERE deleted_at IS NULL GROUP BY mime_type ORDER BY total DESC LIMIT 10');
        $recent = $this->connection->query('SELECT original_name, mime_type, file_size, created_at FROM media WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 10');

        return array(
            'by_type' => $byType ? $byType->fetchAll(PDO::FETCH_ASSOC) : array(),
            'recent' => $recent ? $recent->fetchAll(PDO::FETCH_ASSOC) : array(),
            'totals' => array(
                'count' => $this->scalar('SELECT COUNT(*) FROM media WHERE deleted_at IS NULL'),
                'bytes' => $this->scalar('SELECT COALESCE(SUM(file_size), 0) FROM media WHERE deleted_at IS NULL'),
            ),
        );
    }

    public function searchStats(): array
    {
        if (!$this->hasTable('search_queries')) {
            return array('popular' => array(), 'daily' => array(), 'totals' => array('count' => 0));
        }

        $popular = $this->connection->query('SELECT normalized_term, COUNT(*) AS total, AVG(result_count) AS avg_results FROM search_queries GROUP BY normalized_term ORDER BY total DESC LIMIT 15');
        $daily = $this->connection->query('SELECT DATE(created_at) AS search_date, COUNT(*) AS total FROM search_queries GROUP BY DATE(created_at) ORDER BY search_date DESC LIMIT 14');

        return array(
            'popular' => $popular ? $popular->fetchAll(PDO::FETCH_ASSOC) : array(),
            'daily' => $daily ? $daily->fetchAll(PDO::FETCH_ASSOC) : array(),
            'totals' => array('count' => $this->scalar('SELECT COUNT(*) FROM search_queries')),
        );
    }

    public function userStats(): array
    {
        $byRole = $this->connection->query('SELECT COALESCE(r.name, "Unknown") AS role_name, COUNT(u.id) AS total FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.deleted_at IS NULL GROUP BY r.id, r.name ORDER BY total DESC, role_name ASC');
        $recent = $this->connection->query('SELECT name, email, created_at, last_login, is_active FROM users WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 10');
        $daily = $this->connection->query('SELECT DATE(created_at) AS sign_up_date, COUNT(*) AS total FROM users WHERE deleted_at IS NULL GROUP BY DATE(created_at) ORDER BY sign_up_date DESC LIMIT 14');

        return array(
            'by_role' => $byRole ? $byRole->fetchAll(PDO::FETCH_ASSOC) : array(),
            'recent' => $recent ? $recent->fetchAll(PDO::FETCH_ASSOC) : array(),
            'daily' => $daily ? $daily->fetchAll(PDO::FETCH_ASSOC) : array(),
        );
    }

    public function activityStats(): array
    {
        $auditToday = $this->hasTable('audit_logs') ? $this->scalar('SELECT COUNT(*) FROM audit_logs WHERE DATE(created_at) = CURRENT_DATE') : 0;
        $engagementToday = $this->hasTable('activity_events') ? $this->scalar('SELECT COUNT(*) FROM activity_events WHERE DATE(created_at) = CURRENT_DATE') : 0;

        $auditRecent = array();
        if ($this->hasTable('audit_logs')) {
            $auditStatement = $this->connection->query('SELECT action, description, created_at FROM audit_logs ORDER BY created_at DESC LIMIT 15');
            $auditRecent = $auditStatement ? $auditStatement->fetchAll(PDO::FETCH_ASSOC) : array();
        }

        return array(
            'today' => array(
                'audit_logs' => $auditToday,
                'activity_events' => $engagementToday,
            ),
            'recent_audit' => $auditRecent,
        );
    }

    public function systemInfo(): array
    {
        $dbVersion = '';

        try {
            $dbVersion = (string) $this->connection->query('SELECT VERSION()')->fetchColumn();
        } catch (\Throwable $exception) {
            $dbVersion = 'Unknown';
        }

        return array(
            'php_version' => PHP_VERSION,
            'sapi' => PHP_SAPI,
            'os' => PHP_OS_FAMILY,
            'db_version' => $dbVersion,
            'memory_limit' => (string) ini_get('memory_limit'),
            'upload_max_filesize' => (string) ini_get('upload_max_filesize'),
            'post_max_size' => (string) ini_get('post_max_size'),
            'timezone' => (string) date_default_timezone_get(),
        );
    }

    private function scalar(string $sql): int
    {
        try {
            return (int) $this->connection->query($sql)->fetchColumn();
        } catch (\Throwable $exception) {
            return 0;
        }
    }

    private function hasTable(string $tableName): bool
    {
        if (isset($this->tableCache[$tableName])) {
            return $this->tableCache[$tableName];
        }

        try {
            $statement = $this->connection->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name LIMIT 1');
            $statement->execute(array('table_name' => $tableName));
            $this->tableCache[$tableName] = (bool) $statement->fetchColumn();
        } catch (\Throwable $exception) {
            $this->tableCache[$tableName] = false;
        }

        return $this->tableCache[$tableName];
    }

    private function humanFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power > 0 ? 2 : 0) . ' ' . $units[$power];
    }
}