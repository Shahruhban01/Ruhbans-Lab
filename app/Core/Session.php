<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    private bool $started = false;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function start(): void
    {
        if ($this->started) {
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name((string) ($this->config['name'] ?? 'app_session'));

            session_set_cookie_params([
                'lifetime' => ((int) ($this->config['lifetime'] ?? 120)) * 60,
                'path' => (string) ($this->config['path'] ?? '/'),
                'secure' => (bool) ($this->config['secure'] ?? false),
                'httponly' => (bool) ($this->config['http_only'] ?? true),
                'samesite' => (string) ($this->config['same_site'] ?? 'Lax'),
            ]);

            session_start();
        }

        $this->started = true;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function pullFlash(string $key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;

        unset($_SESSION['_flash'][$key]);

        return $value;
    }

    public function token(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['_csrf_token'];
    }

    public function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }
}
