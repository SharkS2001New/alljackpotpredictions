<?php
/**
 * Redis connections — same contract as baopredictions / pitchpredictionsbackend.
 * In-cluster k3s: host "redis", password from Secret redis-auth.
 */
require __DIR__ . '/load-env.php';

return [
    'client' => bao_env('REDIS_CLIENT', 'predis'),

    'options' => [
        'prefix' => bao_env('REDIS_PREFIX', 'ajp_database_'),
    ],

    'default' => [
        'url' => bao_env('REDIS_URL'),
        'host' => bao_env('REDIS_HOST', 'redis'),
        'username' => bao_env('REDIS_USERNAME'),
        'password' => bao_env('REDIS_PASSWORD'),
        'port' => (int) bao_env('REDIS_PORT', 6379),
        'database' => (int) bao_env('REDIS_DB', 0),
        'timeout' => 1.5,
        'read_write_timeout' => 1.5,
    ],

    // Laravel-style cache store connection (Predis DB for tip/stats payloads).
    'cache' => [
        'url' => bao_env('REDIS_URL'),
        'host' => bao_env('REDIS_HOST', 'redis'),
        'username' => bao_env('REDIS_USERNAME'),
        'password' => bao_env('REDIS_PASSWORD'),
        'port' => (int) bao_env('REDIS_PORT', 6379),
        'database' => (int) bao_env('REDIS_CACHE_DB', 1),
        'timeout' => 1.5,
        'read_write_timeout' => 1.5,
    ],
];
