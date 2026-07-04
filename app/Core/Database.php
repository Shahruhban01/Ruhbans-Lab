<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $driver = (string) ($this->config['driver'] ?? 'mysql');
        $host = (string) ($this->config['host'] ?? '127.0.0.1');
        $port = (int) ($this->config['port'] ?? 3306);
        $database = (string) ($this->config['database'] ?? '');
        $charset = (string) ($this->config['charset'] ?? 'utf8mb4');
        $dsn = sprintf('%s:host=%s;port=%d;dbname=%s;charset=%s', $driver, $host, $port, $database, $charset);

        try {
            self::$connection = new PDO(
                $dsn,
                (string) ($this->config['username'] ?? ''),
                (string) ($this->config['password'] ?? ''),
                (array) ($this->config['options'] ?? [])
            );

            $timezone = (string) ($this->config['timezone'] ?? '+00:00');
            self::$connection->exec("SET time_zone = '{$timezone}'");
        } catch (PDOException $exception) {
            throw new \RuntimeException('Database connection failed.', 0, $exception);
        }

        return self::$connection;
    }
}
