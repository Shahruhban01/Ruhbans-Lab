<?php

declare(strict_types=1);

namespace App\Core;

final class ErrorHandler
{
    private bool $debug;
    private Logger $logger;
    private BaseView $view;

    public function __construct(bool $debug, Logger $logger, BaseView $view)
    {
        $this->debug = $debug;
        $this->logger = $logger;
        $this->view = $view;
    }

    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        $this->logger->error($message, compact('severity', 'file', 'line'));

        if ($this->debug) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        }

        return true;
    }

    public function handleException(\Throwable $exception): void
    {
        $this->logger->critical($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $this->debug ? $exception->getTraceAsString() : null,
        ]);

        $statusCode = $this->normalizeStatusCode($exception->getCode());

        if (!headers_sent()) {
            http_response_code($statusCode);
        }

        $template = 'errors/500';

        if ($statusCode === 404) {
            $template = 'errors/404';
        } elseif ($statusCode === 403) {
            $template = 'errors/403';
        } elseif ($statusCode === 419) {
            $template = 'errors/419';
        } elseif ($statusCode === 503) {
            $template = 'errors/503';
        }

        echo $this->view->render($template, [
            'exception' => $this->debug ? $exception : null,
        ], [
            'meta' => [
                'title' => $statusCode === 404 ? 'Page Not Found' : ($statusCode === 403 ? 'Forbidden' : ($statusCode === 419 ? 'Session Expired' : 'Server Error')),
                'robots' => 'noindex, nofollow',
            ],
            'layout' => false,
        ]);
    }

    private function normalizeStatusCode($code): int
    {
        if (is_int($code) && $code >= 400 && $code <= 599) {
            return $code;
        }

        if (is_string($code) && ctype_digit($code)) {
            $statusCode = (int) $code;

            if ($statusCode >= 400 && $statusCode <= 599) {
                return $statusCode;
            }
        }

        return 500;
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            $this->logger->critical($error['message'], $error);
        }
    }
}
