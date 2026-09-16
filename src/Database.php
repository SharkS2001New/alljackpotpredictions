<?php
namespace App;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Shared PDO connection to pitchnewdb.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $cfg = require dirname(__DIR__) . '/config/database.php';

        if (!function_exists('bao_env')) {
            require_once dirname(__DIR__) . '/config/load-env.php';
        }
        // Persistent PDO multiplies MySQL connections by FPM workers and holds
        // sockets across requests — off by default. Set PDO_PERSISTENT=1 to enable.
        $persistent = filter_var(bao_env('PDO_PERSISTENT', '0'), FILTER_VALIDATE_BOOLEAN);
        // CLI/cron should never use persistent connections.
        if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
            $persistent = false;
        }

        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['driver'] ?? 'mysql',
            $cfg['host'],
            (int) ($cfg['port'] ?? 3306),
            $cfg['database'],
            $cfg['charset'] ?? 'utf8mb4'
        );

        try {
            self::$pdo = new PDO(
                $dsn,
                (string) $cfg['username'],
                (string) $cfg['password'],
                array_replace([
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_PERSISTENT => $persistent,
                ], $cfg['options'] ?? [])
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }

        return self::$pdo;
    }
}
