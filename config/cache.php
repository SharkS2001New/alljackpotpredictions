<?php
/**
 * Cache config — same Predis/file contract as baopredictions / pitchpredictionsbackend.
 * k3s: CACHE_DRIVER=redis (injected by Helm redisEnv → REDIS_CACHE_DB).
 */
require __DIR__ . '/load-env.php';

$appEnv = (string) bao_env('APP_ENV', 'local');

return [
    // Same default rule as bao: redis in production, file otherwise.
    'default' => bao_env('CACHE_DRIVER', $appEnv === 'production' ? 'redis' : 'file'),

    // Isolate AJP keys on shared Redis (bao uses pitch_predictions_cache_).
    'prefix' => bao_env('CACHE_PREFIX', 'ajp_cache_'),

    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => dirname(__DIR__) . '/storage/framework/cache/data',
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
        ],
    ],
];
