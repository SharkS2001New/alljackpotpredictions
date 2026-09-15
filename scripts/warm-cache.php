<?php
/**
 * Warm shared API caches (stats, homepage, jackpots, sidebar extras).
 * Run via cron every 5–10 minutes, or: php scripts/warm-cache.php
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/api-curl.php';

$started = microtime(true);
$paths = [
    'stats',
    'homepage',
    'football-predictions-today',
    'btts-predictions',
    '1x2-predictions',
    'accumulator-tips',
    'results',
    'jackpot-predictions',
    'sportpesa-mega-jackpot-predictions',
    'sportpesa-midweek-jackpot-predictions',
    'betika-midweek-jackpot-predictions',
];

foreach ($paths as $path) {
    $t = microtime(true);
    if ($path === 'stats') {
        $ok = ajp_warm_stats() !== null;
    } else {
        // Bypass cache_only / stale short-circuit by forgetting then fetching
        $meta = ajp_api_cache_meta($path);
        \App\Support\Cache::forgetCachedJsonPayload($meta['key']);
        $payload = ajp_curl_api('/api/' . $path, ['allow_stale' => false]);
        $ok = is_array($payload) && ($payload['ok'] ?? true);
    }
    $ms = (int) round((microtime(true) - $t) * 1000);
    echo ($ok ? 'OK' : 'FAIL') . "  {$path}  {$ms}ms\n";
}

echo 'total ' . (int) round((microtime(true) - $started) * 1000) . "ms\n";
