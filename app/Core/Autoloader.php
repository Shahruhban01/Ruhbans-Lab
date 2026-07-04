<?php

declare(strict_types=1);

namespace App\Core;

final class Autoloader
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '\\/');
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    private function loadClass(string $class): void
    {
        $prefix = 'App\\';

        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $this->basePath . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
}
