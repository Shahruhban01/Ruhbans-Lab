<?php

declare(strict_types=1);

use App\Core\Application;

if (!function_exists('app')) {
    function app(): Application
    {
        return Application::getInstance();
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $basePath = app()->basePath();

        return $path === '' ? $basePath : $basePath . '/' . ltrim($path, '/\\');
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        return app()->config()->get($key, $default);
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return \App\Core\Environment::get($key, $default);
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path !== '' ? '/' . ltrim($path, '/\\') : ''));
    }
}

if (!function_exists('app_base_url')) {
    function app_base_url(): string
    {
        $configuredUrl = rtrim((string) config('app.url', ''), '/');
        $parsedUrl = parse_url($configuredUrl);
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : 'http';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : 'localhost';
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $basePath = '';

        if (!empty($parsedUrl['path']) && $parsedUrl['path'] !== '/') {
            $basePath = '/' . trim($parsedUrl['path'], '/');
        } else {
            $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
            $scriptBase = trim(dirname($scriptName), '/');

            if ($scriptBase !== '' && substr($scriptBase, -7) === '/public') {
                $scriptBase = trim(substr($scriptBase, 0, -7), '/');
            }

            if ($scriptBase !== '' && $scriptBase !== '.') {
                $basePath = '/' . $scriptBase;
            }
        }

        return $scheme . '://' . $host . $port . $basePath;
    }
}

if (!function_exists('asset')) {
    function asset(string $path = ''): string
    {
        return rtrim(app_base_url(), '/') . '/' . ltrim($path, '/\\');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $path = '/' . ltrim($path, '/\\');

        return rtrim(app_base_url(), '/') . ($path === '/' ? '' : $path);
    }
}

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('slugify')) {
    function slugify($value): string
    {
        $value = strtolower(trim((string) $value));
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
        $value = trim((string) $value, '-');

        return $value !== '' ? $value : 'item';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return app()->session()->token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('request')) {
    function request(): \App\Core\Request
    {
        return app()->request();
    }
}

if (!function_exists('logger')) {
    function logger(): \App\Core\Logger
    {
        return app()->logger();
    }
}

if (!function_exists('db')) {
    function db(): \PDO
    {
        return app()->database()->connection();
    }
}

if (!function_exists('view')) {
    function view(string $template, array $data = [], array $options = []): string
    {
        return app()->view()->render($template, $data, $options);
    }
}
