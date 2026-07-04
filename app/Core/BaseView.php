<?php

declare(strict_types=1);

namespace App\Core;

final class BaseView
{
    private string $viewsPath;
    private Config $config;

    public function __construct(string $viewsPath, Config $config)
    {
        $this->viewsPath = $viewsPath;
        $this->config = $config;
    }

    public function render(string $view, array $data = [], array $options = []): string
    {
        $layout = $options['layout'] ?? 'layouts/main';
        $viewFile = $this->resolveViewPath($view);

        if (!is_file($viewFile)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        extract($data, EXTR_SKIP);
        $meta = $options['meta'] ?? [];

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        if ($layout === false) {
            return $content;
        }

        $layoutFile = $this->resolveViewPath((string) $layout);

        if (!is_file($layoutFile)) {
            throw new \RuntimeException('Layout not found: ' . $layout);
        }

        ob_start();
        require $layoutFile;

        return (string) ob_get_clean();
    }

    public function exists(string $view): bool
    {
        return is_file($this->resolveViewPath($view));
    }

    private function resolveViewPath(string $view): string
    {
        return rtrim($this->viewsPath, '\\/') . '/' . trim(str_replace('.', '/', $view), '/') . '.php';
    }
}
