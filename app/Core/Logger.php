<?php

declare(strict_types=1);

namespace App\Core;

final class Logger
{
    private string $logPath;
    private string $channel;

    public function __construct(string $logPath, string $channel = 'single')
    {
        $this->logPath = $logPath;
        $this->channel = $channel;
    }

    public function debug(string $message, array $context = []): void
    {
        $this->write('DEBUG', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->write('CRITICAL', $message, $context);
    }

    private function write(string $level, string $message, array $context = []): void
    {
        if ($this->channel !== 'single') {
            return;
        }

        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }

        $entry = sprintf(
            "[%s] [%s] %s %s%s",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $context !== [] ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '',
            PHP_EOL
        );

        file_put_contents($this->logPath . '/app-' . date('Y-m-d') . '.log', $entry, FILE_APPEND | LOCK_EX);
    }
}
