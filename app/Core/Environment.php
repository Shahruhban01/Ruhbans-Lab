<?php

declare(strict_types=1);

namespace App\Core;

final class Environment
{
    private static array $loaded = [];

    public static function load(string $path): void
    {
        $file = rtrim($path, '\\/') . '/.env';

        if (!is_file($file) || isset(self::$loaded[$file])) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || self::startsWith($trimmed, '#') || strpos($trimmed, '=') === false) {
                continue;
            }

            [$name, $value] = explode('=', $trimmed, 2);
            $name = trim($name);
            $value = trim($value);

            if ($value !== '' && (($value[0] === '"' && self::endsWith($value, '"')) || ($value[0] === '\'' && self::endsWith($value, '\'')))) {
                $value = substr($value, 1, -1);
            }

            $normalized = self::normalizeValue($value);

            $_ENV[$name] = $normalized;
            $_SERVER[$name] = $normalized;
            putenv($name . '=' . $normalized);
        }

        self::$loaded[$file] = true;
    }

    public static function get(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        return $value === false || $value === null || $value === '' ? $default : $value;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    public static function int(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    private static function normalizeValue(string $value)
    {
        $normalized = strtolower($value);

        if ($normalized === 'true' || $normalized === '(true)') {
            return true;
        }

        if ($normalized === 'false' || $normalized === '(false)') {
            return false;
        }

        if ($normalized === 'null' || $normalized === '(null)') {
            return null;
        }

        if ($normalized === 'empty' || $normalized === '(empty)') {
            return '';
        }

        return $value;
    }

    private static function startsWith(string $value, string $prefix): bool
    {
        return substr($value, 0, strlen($prefix)) === $prefix;
    }

    private static function endsWith(string $value, string $suffix): bool
    {
        if ($suffix === '') {
            return true;
        }

        return substr($value, -strlen($suffix)) === $suffix;
    }
}
