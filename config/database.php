<?php
/**
 * Database connection settings for pitchnewdb (fixtures / predictions / odds).
 * Values load from project-root .env. Never commit real passwords.
 */
require __DIR__ . '/load-env.php';

return [
    'driver' => bao_env('DB_CONNECTION', 'mysql'),
    'host' => bao_env('DB_HOST', '127.0.0.1'),
    'port' => (int) bao_env('DB_PORT', 3306),
    'database' => bao_env('DB_DATABASE', 'pitchnewdb'),
    'username' => bao_env('DB_USERNAME', ''),
    'password' => bao_env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 10,
    ],
];
