<?php
/**
 * Redis connections — mirrors pitchpredictionsbackend/config/database.php redis section.
 * In-cluster k3s service name: redis (see pitchpredk3ssetup redis chart).
 */
require __DIR__ . '/load-env.php';

return [
    'client' => bao_env('REDIS_CLIENT', 'predis'),

    // Same as pitchpredictionsbackend database.php redis.options.prefix
    'options' => [
        'prefix' => bao_env('REDIS_PREFIX', 'pitch_predictions_database_'),
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

    // Laravel cache store connection
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
