<?php
/**
 * Cache config — mirrors pitchpredictionsbackend/config/cache.php
 * k3s: CACHE_DRIVER=redis (Predis → REDIS_CACHE_DB)
 */
require __DIR__ . '/load-env.php';

$appEnv = (string) bao_env('APP_ENV', 'local');

return [
    // Same default rule as Laravel backend: redis in production, file otherwise.
    'default' => bao_env('CACHE_DRIVER', $appEnv === 'production' ? 'redis' : 'file'),

    // Match pitchpredictionsbackend (APP_NAME="Pitch Predictions" → pitch_predictions_cache_).
    // Laravel RedisStore appends ":" after this prefix — see Cache::prefixed().
    'prefix' => bao_env('CACHE_PREFIX', 'pitch_predictions_cache_'),

    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => dirname(__DIR__) . '/storage/framework/cache/data',
        ],
        'redis' => [
            'driver' => 'redis',
            // Laravel: redis store uses the "cache" connection (REDIS_CACHE_DB).
            'connection' => 'cache',
        ],
    ],
];
