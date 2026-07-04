<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    private string $configPath;
    private array $items = [];

    public function __construct(string $configPath)
    {
        $this->configPath = rtrim($configPath, '\\/');
    }

    public function load(string $file): array
    {
        if (!isset($this->items[$file])) {
            $path = $this->configPath . '/' . ltrim($file, '/');
            $this->items[$file] = is_file($path) ? require $path : [];
        }

        return $this->items[$file];
    }

    public function all(): array
    {
        return $this->items;
    }

    public function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);
        $data = $this->load($file . '.php');

        foreach ($segments as $segment) {
            if (!is_array($data) || !array_key_exists($segment, $data)) {
                return $default;
            }

            $data = $data[$segment];
        }

        return $data;
    }
}
