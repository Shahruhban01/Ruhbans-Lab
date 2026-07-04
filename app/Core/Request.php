<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    private array $server;
    private array $get;
    private array $post;
    private array $files;

    private function __construct(array $server, array $get, array $post, array $files)
    {
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
    }

    public static function fromGlobals(): self
    {
        return new self($_SERVER, $_GET, $_POST, $_FILES);
    }

    public function method(): string
    {
        $method = strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));

        if ($method === 'POST' && isset($this->post['_method'])) {
            return strtoupper((string) $this->post['_method']);
        }

        return $method;
    }

    public function path(): string
    {
        $path = (string) ($this->server['REQUEST_URI'] ?? '/');
        $path = parse_url($path, PHP_URL_PATH) ?: '/';

        $basePath = $this->basePath();
        if ($basePath !== '' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        if ($path === '') {
            $path = '/';
        }

        return '/' . trim($path, '/');
    }

    public function fullUrl(): string
    {
        $scheme = $this->isSecure() ? 'https' : 'http';
        $host = (string) ($this->server['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host . ($this->server['REQUEST_URI'] ?? '/');
    }

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function only(array $keys): array
    {
        $data = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $this->post)) {
                $data[$key] = $this->post[$key];
            } elseif (array_key_exists($key, $this->get)) {
                $data[$key] = $this->get[$key];
            }
        }

        return $data;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function file(string $key)
    {
        return $this->files[$key] ?? null;
    }

    public function header(string $key, $default = null)
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $key));

        return $this->server[$normalized] ?? $default;
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '127.0.0.1');
    }

    public function isSecure(): bool
    {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') || (int) ($this->server['SERVER_PORT'] ?? 80) === 443;
    }

    public function expectsJson(): bool
    {
        $accept = (string) $this->header('Accept', '');

        return strpos($accept, 'application/json') !== false || strpos($accept, 'application/vnd.api+json') !== false;
    }

    private function basePath(): string
    {
        $scriptName = str_replace('\\', '/', (string) ($this->server['SCRIPT_NAME'] ?? ''));
        $basePath = trim(dirname($scriptName), '/');

        if ($basePath !== '' && substr($basePath, -7) === '/public') {
            $basePath = trim(substr($basePath, 0, -7), '/');
        }

        return $basePath !== '' ? '/' . $basePath : '';
    }
}
