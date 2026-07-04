<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class SettingRepository extends BaseRepository
{
    protected string $table = 'settings';

    private ?bool $settingsTableExists = null;

    public function allGrouped(): array
    {
        if (!$this->hasSettingsTable()) {
            return array();
        }

        $statement = $this->connection->query('SELECT * FROM settings ORDER BY group_key ASC, setting_key ASC');
        $rows = $statement ? $statement->fetchAll(PDO::FETCH_ASSOC) : array();
        $grouped = array();

        foreach ($rows as $row) {
            $group = isset($row['group_key']) ? (string) $row['group_key'] : 'general';
            $key = isset($row['setting_key']) ? (string) $row['setting_key'] : '';

            if ($key === '') {
                continue;
            }

            if (!isset($grouped[$group])) {
                $grouped[$group] = array();
            }

            $grouped[$group][$key] = $this->decodeValue($row);
        }

        return $grouped;
    }

    public function value(string $settingKey, $default = null)
    {
        if (!$this->hasSettingsTable()) {
            return $default;
        }

        $statement = $this->connection->prepare('SELECT * FROM settings WHERE setting_key = :setting_key LIMIT 1');
        $statement->execute(array('setting_key' => $settingKey));
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return $default;
        }

        return $this->decodeValue($row);
    }

    public function saveGroup(string $groupKey, array $values, ?int $userId = null): void
    {
        if (!$this->hasSettingsTable()) {
            return;
        }

        foreach ($values as $key => $value) {
            $settingKey = (string) $key;

            if ($settingKey === '' || strpos($settingKey, $groupKey . '.') !== 0) {
                continue;
            }

            $this->upsert($groupKey, $settingKey, $value, $userId);
        }
    }

    private function upsert(string $groupKey, string $settingKey, $value, ?int $userId): void
    {
        $encoded = $this->encodeValue($value);
        $statement = $this->connection->prepare('INSERT INTO settings (group_key, setting_key, setting_value, value_type, is_public, updated_by, created_at, updated_at) VALUES (:group_key, :setting_key, :setting_value, :value_type, :is_public, :updated_by, :created_at, :updated_at) ON DUPLICATE KEY UPDATE group_key = VALUES(group_key), setting_value = VALUES(setting_value), value_type = VALUES(value_type), is_public = VALUES(is_public), updated_by = VALUES(updated_by), updated_at = VALUES(updated_at)');
        $now = date('Y-m-d H:i:s');
        $statement->execute(array(
            'group_key' => $groupKey,
            'setting_key' => $settingKey,
            'setting_value' => $encoded['value'],
            'value_type' => $encoded['type'],
            'is_public' => $this->isPublicSetting($settingKey) ? 1 : 0,
            'updated_by' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ));
    }

    private function decodeValue(array $row)
    {
        $type = isset($row['value_type']) ? (string) $row['value_type'] : 'string';
        $value = isset($row['setting_value']) ? (string) $row['setting_value'] : '';

        if ($type === 'bool') {
            return $value === '1';
        }

        if ($type === 'int') {
            return (int) $value;
        }

        if ($type === 'json') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : array();
        }

        return $value;
    }

    private function encodeValue($value): array
    {
        if (is_bool($value)) {
            return array('type' => 'bool', 'value' => $value ? '1' : '0');
        }

        if (is_int($value)) {
            return array('type' => 'int', 'value' => (string) $value);
        }

        if (is_array($value)) {
            return array('type' => 'json', 'value' => (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return array('type' => 'string', 'value' => trim((string) $value));
    }

    private function isPublicSetting(string $settingKey): bool
    {
        foreach (array('general.', 'seo.', 'branding.', 'theme.', 'social.') as $prefix) {
            if (strpos($settingKey, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    private function hasSettingsTable(): bool
    {
        if ($this->settingsTableExists !== null) {
            return $this->settingsTableExists;
        }

        try {
            $statement = $this->connection->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name LIMIT 1');
            $statement->execute(array('table_name' => 'settings'));
            $this->settingsTableExists = (bool) $statement->fetchColumn();
        } catch (\Throwable $exception) {
            $this->settingsTableExists = false;
        }

        return $this->settingsTableExists;
    }
}