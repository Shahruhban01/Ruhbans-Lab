<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Application;
use App\Core\Request;
use App\Repositories\AuditLogRepository;
use App\Repositories\AnalyticsRepository;
use App\Repositories\SettingRepository;
use App\Services\Admin\SettingsService;

final class SettingsController extends BaseAdminController
{
    private SettingsService $settingsService;
    private AuditLogRepository $auditLogRepository;
    private AnalyticsRepository $analyticsRepository;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $connection = $this->app->database()->connection();
        $this->settingsService = new SettingsService(new SettingRepository($connection));
        $this->auditLogRepository = new AuditLogRepository($connection);
        $this->analyticsRepository = new AnalyticsRepository($connection);
    }

    public function index(Request $request)
    {
        return $this->adminView('admin/settings/index', array(
            'currentUser' => $this->currentUser(),
            'settings' => $this->settingsService->all(),
            'maintenanceMode' => $this->isMaintenanceMode(),
            'backupFiles' => $this->backupFiles(),
            'systemInfo' => $this->analyticsRepository->systemInfo(),
        ), array(
            'title' => 'Settings',
            'description' => 'General, SEO, SMTP, branding, theme, and system settings.',
            'canonical' => url('/admin/settings'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function saveGroup(Request $request, string $group)
    {
        try {
            $this->settingsService->saveGroup($group, $request->all(), $this->currentUserId());
            $this->auditLogRepository->createLog($this->currentUserId(), 'settings_updated', 'Updated settings group.', array('group' => $group), $request->ip(), (string) $request->header('User-Agent', ''));
            $this->app->session()->flash('success', ucfirst($group) . ' settings updated.');
        } catch (\Throwable $exception) {
            $this->app->session()->flash('error', $exception->getMessage());
        }

        return $this->redirect('/admin/settings');
    }

    public function clearCache(Request $request)
    {
        $cachePath = base_path('cache');
        $removed = 0;

        if (is_dir($cachePath)) {
            $entries = scandir($cachePath);
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..' || $entry === '.gitignore') {
                    continue;
                }

                $fullPath = $cachePath . DIRECTORY_SEPARATOR . $entry;
                if (is_file($fullPath)) {
                    if (@unlink($fullPath)) {
                        $removed++;
                    }
                }
            }
        }

        $this->auditLogRepository->createLog($this->currentUserId(), 'cache_cleared', 'Cleared application cache.', array('removed_files' => $removed), $request->ip(), (string) $request->header('User-Agent', ''));
        $this->app->session()->flash('success', 'Cache cleared. Removed files: ' . $removed . '.');

        return $this->redirect('/admin/settings');
    }

    public function backup(Request $request)
    {
        try {
            $filePath = $this->createBackupFile();
            $this->auditLogRepository->createLog($this->currentUserId(), 'backup_created', 'Created backup snapshot.', array('file' => basename($filePath)), $request->ip(), (string) $request->header('User-Agent', ''));
            $this->app->session()->flash('success', 'Backup created: ' . basename($filePath));
        } catch (\Throwable $exception) {
            $this->app->session()->flash('error', $exception->getMessage());
        }

        return $this->redirect('/admin/settings');
    }

    public function restore(Request $request)
    {
        $file = $request->file('backup_file');

        if (!is_array($file) || empty($file['tmp_name']) || !is_uploaded_file((string) $file['tmp_name'])) {
            $this->app->session()->flash('error', 'Select a valid backup file.');
            return $this->redirect('/admin/settings');
        }

        try {
            $json = (string) file_get_contents((string) $file['tmp_name']);
            $payload = json_decode($json, true);

            if (!is_array($payload) || !isset($payload['tables']) || !is_array($payload['tables'])) {
                throw new \RuntimeException('Invalid backup payload.');
            }

            $restored = $this->restoreFromPayload($payload);
            $this->auditLogRepository->createLog($this->currentUserId(), 'backup_restored', 'Restored backup snapshot.', array('tables' => $restored), $request->ip(), (string) $request->header('User-Agent', ''));
            $this->app->session()->flash('success', 'Restore completed.');
        } catch (\Throwable $exception) {
            $this->app->session()->flash('error', $exception->getMessage());
        }

        return $this->redirect('/admin/settings');
    }

    public function maintenance(Request $request)
    {
        $enabled = (string) $request->input('enabled', '0') === '1';
        $flagPath = storage_path('maintenance.flag');

        if ($enabled) {
            if (!is_dir(dirname($flagPath))) {
                mkdir(dirname($flagPath), 0755, true);
            }
            file_put_contents($flagPath, (string) time());
        } else {
            if (is_file($flagPath)) {
                @unlink($flagPath);
            }
        }

        $this->settingsService->saveGroup('system', array('system__maintenance_mode' => $enabled ? '1' : '0', 'system__cache_ttl_minutes' => $request->input('system__cache_ttl_minutes', 60)), $this->currentUserId());
        $this->auditLogRepository->createLog($this->currentUserId(), 'maintenance_toggled', $enabled ? 'Enabled maintenance mode.' : 'Disabled maintenance mode.', array('enabled' => $enabled), $request->ip(), (string) $request->header('User-Agent', ''));
        $this->app->session()->flash('success', $enabled ? 'Maintenance mode enabled.' : 'Maintenance mode disabled.');

        return $this->redirect('/admin/settings');
    }


    private function backupFiles(): array
    {
        $backupPath = storage_path('backups');

        if (!is_dir($backupPath)) {
            return array();
        }

        $files = array();
        foreach (scandir($backupPath) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $fullPath = $backupPath . DIRECTORY_SEPARATOR . $file;
            if (!is_file($fullPath)) {
                continue;
            }

            $files[] = array(
                'name' => $file,
                'size' => filesize($fullPath),
                'modified_at' => date('Y-m-d H:i:s', filemtime($fullPath)),
            );
        }

        usort($files, static function (array $a, array $b): int {
            return strcmp($b['modified_at'], $a['modified_at']);
        });

        return $files;
    }

    private function createBackupFile(): string
    {
        $backupPath = storage_path('backups');

        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $tables = array(
            'roles',
            'users',
            'content_types',
            'categories',
            'tags',
            'posts',
            'post_categories',
            'post_tags',
            'post_meta',
            'post_seo',
            'post_revisions',
            'media',
            'search_queries',
            'content_metrics',
            'comments',
            'content_interactions',
            'reading_history',
            'notifications',
            'activity_events',
            'newsletter_subscribers',
            'contact_messages',
            'redirects',
            'settings',
        );

        $payload = array(
            'meta' => array(
                'created_at' => date('Y-m-d H:i:s'),
                'version' => 'phase9',
                'app' => (string) config('app.name', 'Developer Ruhban'),
            ),
            'tables' => array(),
        );

        $connection = $this->app->database()->connection();
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                continue;
            }

            $statement = $connection->query('SELECT * FROM ' . $table);
            $payload['tables'][$table] = $statement ? $statement->fetchAll(\PDO::FETCH_ASSOC) : array();
        }

        $filename = 'backup-' . date('Ymd-His') . '.json';
        $filePath = $backupPath . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($filePath, (string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return $filePath;
    }

    private function restoreFromPayload(array $payload): array
    {
        $connection = $this->app->database()->connection();
        $tables = isset($payload['tables']) && is_array($payload['tables']) ? $payload['tables'] : array();
        $restoreOrder = array(
            'roles',
            'users',
            'content_types',
            'categories',
            'tags',
            'posts',
            'post_categories',
            'post_tags',
            'post_meta',
            'post_seo',
            'post_revisions',
            'media',
            'search_queries',
            'content_metrics',
            'comments',
            'content_interactions',
            'reading_history',
            'notifications',
            'activity_events',
            'newsletter_subscribers',
            'contact_messages',
            'redirects',
            'settings',
        );

        $restoredTables = array();
        $connection->beginTransaction();

        try {
            $connection->exec('SET FOREIGN_KEY_CHECKS=0');

            foreach ($restoreOrder as $table) {
                if (!isset($tables[$table]) || !$this->tableExists($table)) {
                    continue;
                }

                $rows = is_array($tables[$table]) ? $tables[$table] : array();
                $connection->exec('TRUNCATE TABLE ' . $table);

                if ($rows !== array()) {
                    $first = reset($rows);
                    if (is_array($first) && $first !== array()) {
                        $columns = array_keys($first);
                        $columnSql = implode(', ', $columns);
                        $placeholderSql = implode(', ', array_map(static function (string $column): string {
                            return ':' . $column;
                        }, $columns));
                        $statement = $connection->prepare('INSERT INTO ' . $table . ' (' . $columnSql . ') VALUES (' . $placeholderSql . ')');

                        foreach ($rows as $row) {
                            if (!is_array($row)) {
                                continue;
                            }

                            $bindings = array();
                            foreach ($columns as $column) {
                                $bindings[$column] = array_key_exists($column, $row) ? $row[$column] : null;
                            }
                            $statement->execute($bindings);
                        }
                    }
                }

                $restoredTables[] = $table;
            }

            $connection->exec('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            try {
                $connection->exec('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Throwable $innerException) {
            }
            throw $exception;
        }

        return $restoredTables;
    }

    private function tableExists(string $tableName): bool
    {
        try {
            $statement = $this->app->database()->connection()->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name LIMIT 1');
            $statement->execute(array('table_name' => $tableName));

            return (bool) $statement->fetchColumn();
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function isMaintenanceMode(): bool
    {
        return is_file(storage_path('maintenance.flag'));
    }
}