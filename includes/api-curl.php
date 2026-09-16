<?php
/**
 * Bao-style in-process page API + TipOracle-shaped fixture adapter.
 * Pages call ajp_fetch_fixtures('btts-predictions') instead of curling tiporacle.com.
 */
require_once __DIR__ . '/../src/Api/bootstrap.php';

use App\Support\Cache;
use App\Support\DateTimeHelper;

/**
 * @return array{key:string,ttl:int}
 */
function ajp_api_cache_meta(string $path): array
{
    $today = DateTimeHelper::siteToday();
    $cacheKey = 'ajp_api_' . str_replace('-', '_', $path) . '_' . $today;

    if ($path === 'stats') {
        // v9: 24h Redis TTL + lighter stats rebuild (counts from warm page cache).
        return ['key' => $cacheKey . '_v9', 'ttl' => Cache::ttlStats()];
    }

    $pages = require dirname(__DIR__) . '/config/api-pages.php';
    $def = is_array($pages[$path] ?? null) ? $pages[$path] : [];

    if (!empty($def['live_only']) || str_contains($path, 'live')) {
        return ['key' => $cacheKey, 'ttl' => Cache::ttlLive()];
    }
    if (($def['range'] ?? '') === 'weekend') {
        return ['key' => $cacheKey, 'ttl' => Cache::ttlWeekend()];
    }
    if (!empty($def['lookback_days'])) {
        return ['key' => $cacheKey . '_v3', 'ttl' => Cache::ttlForSiteDate($today)];
    }

    $source = strtolower((string) ($def['source'] ?? 'fixtures'));
    if ($source === 'selections' || $source === 'jackpot_hub' || str_contains($path, 'jackpot')) {
        return ['key' => $cacheKey . '_jp_v3', 'ttl' => Cache::ttlJackpot()];
    }

    $dayMod = strtolower((string) ($def['day'] ?? 'today'));
    if ($dayMod === '' || isset($def['date'])) {
        $fixtureDate = isset($def['date']) && is_string($def['date']) && $def['date'] !== ''
            ? $def['date']
            : $today;
    } else {
        $fixtureDate = DateTimeHelper::siteDate($dayMod);
    }

    return ['key' => $cacheKey . '_v5', 'ttl' => Cache::ttlForSiteDate($fixtureDate)];
}

/**
 * Load a page API payload in-process (same pattern as bao_curl_api).
 *
 * Options:
 *   cache_only (bool) — never hit DB; return fresh/stale cache or null
 *   allow_stale (bool) — if fresh miss, return expired cache instead of rebuilding (default true for secondary)
 *
 * @param array<string,mixed> $opts
 * @return array<string,mixed>|null
 */
function ajp_curl_api(string $apiPath, array $opts = []): ?array
{
    static $memo = [];

    $path = '/' . ltrim(parse_url($apiPath, PHP_URL_PATH) ?: $apiPath, '/');
    $path = preg_replace('#^/api/#', '', $path) ?? '';
    $path = trim((string) $path, '/');

    if ($path === '' || $path === 'health' || $path === 'pages' || $path === 'games') {
        return null;
    }

    $cacheOnly = !empty($opts['cache_only']);
    $allowStale = array_key_exists('allow_stale', $opts) ? (bool) $opts['allow_stale'] : true;
    $memoKey = $path . ($cacheOnly ? ':co' : '');

    if (isset($memo[$memoKey])) {
        return $memo[$memoKey];
    }

    $meta = ajp_api_cache_meta($path);
    $ttl = (int) $meta['ttl'];
    $cacheKey = $meta['key'];

    try {
        if ($ttl > 0) {
            $cached = Cache::getCachedJsonPayload($cacheKey);
            if (is_array($cached) && ($cached['ok'] ?? false) === true) {
                return $memo[$memoKey] = $cached;
            }
        }

        if ($cacheOnly) {
            if ($ttl > 0 && $allowStale) {
                $stale = Cache::getCachedJsonPayload($cacheKey, true);
                if (is_array($stale) && ($stale['ok'] ?? false) === true) {
                    return $memo[$memoKey] = $stale;
                }
            }
            return $memo[$memoKey] = null;
        }

        // Prefer serving slightly-stale shared payloads (stats) over a multi-second rebuild
        // on every cache miss — rebuild in shutdown after response when possible.
        if ($ttl > 0 && $allowStale && ($path === 'stats' || str_contains($path, 'jackpot'))) {
            $stale = Cache::getCachedJsonPayload($cacheKey, true);
            if (is_array($stale) && ($stale['ok'] ?? false) === true) {
                $memo[$memoKey] = $stale;
                ajp_schedule_cache_refresh($path, $cacheKey, $ttl);
                return $stale;
            }
        }

        // Stats are used in the global header — never block the request on a cold miss.
        // Warm the cache after the response (FPM finish / shutdown).
        if ($path === 'stats') {
            ajp_schedule_cache_refresh($path, $cacheKey, max(60, $ttl));
            return $memo[$memoKey] = null;
        }

        $api = new \App\Services\PageApiService();
        if (!$api->hasPage($path)) {
            return $memo[$memoKey] = null;
        }
        $payload = $api->payload($path);

        if ($ttl > 0 && is_array($payload) && ($payload['ok'] ?? true)) {
            Cache::putCachedJsonPayload($cacheKey, $payload, $ttl);
        }

        return $memo[$memoKey] = $payload;
    } catch (Throwable $e) {
        bao_log_exception($e, 'ajp_curl_api failed', ['path' => $path]);
        if ($ttl > 0 && $allowStale) {
            $stale = Cache::getCachedJsonPayload($cacheKey, true);
            if (is_array($stale)) {
                return $memo[$memoKey] = $stale;
            }
        }
        return $memo[$memoKey] = null;
    }
}

/**
 * Rebuild a cache key after the response is sent (FPM only).
 * Skipped on php -S / SAPIs without fastcgi_finish_request so we never
 * hold the HTTP connection open for a multi-second StatsService rebuild.
 * Stats rebuilds are single-flight across workers via a short file lock.
 */
function ajp_schedule_cache_refresh(string $path, string $cacheKey, int $ttl): void
{
    static $queued = [];
    if (isset($queued[$cacheKey])) {
        return;
    }
    if (!function_exists('fastcgi_finish_request')) {
        return;
    }
    $queued[$cacheKey] = true;

    register_shutdown_function(static function () use ($path, $cacheKey, $ttl): void {
        @fastcgi_finish_request();
        $lockFp = null;
        try {
            if ($path === 'stats') {
                $lockDir = dirname(__DIR__) . '/storage/framework/cache';
                if (!is_dir($lockDir)) {
                    @mkdir($lockDir, 0775, true);
                }
                $lockPath = $lockDir . '/stats-rebuild.lock';
                $lockFp = @fopen($lockPath, 'c+');
                if ($lockFp === false || !flock($lockFp, LOCK_EX | LOCK_NB)) {
                    return; // another worker is already rebuilding
                }
                // Another worker may have filled cache while we waited for the lock attempt.
                $fresh = Cache::getCachedJsonPayload($cacheKey, false);
                if (is_array($fresh) && ($fresh['ok'] ?? false) === true) {
                    return;
                }
                $payload = (new \App\Services\StatsService())->payload();
            } else {
                $api = new \App\Services\PageApiService();
                if (!$api->hasPage($path)) {
                    return;
                }
                $payload = $api->payload($path);
            }
            if ($ttl > 0 && is_array($payload) && ($payload['ok'] ?? true)) {
                Cache::putCachedJsonPayload($cacheKey, $payload, $ttl);
            }
        } catch (Throwable $e) {
            if (function_exists('bao_log_exception')) {
                bao_log_exception($e, 'ajp_schedule_cache_refresh failed', ['path' => $path]);
            }
        } finally {
            if (is_resource($lockFp)) {
                flock($lockFp, LOCK_UN);
                fclose($lockFp);
            }
        }
    });
}

/** Force-rebuild stats into cache (call from track-record / cron / warm script). */
function ajp_warm_stats(): ?array
{
    $meta = ajp_api_cache_meta('stats');
    try {
        $payload = (new \App\Services\StatsService())->payload();
        if (is_array($payload) && ($payload['ok'] ?? true)) {
            Cache::putCachedJsonPayload($meta['key'], $payload, max(60, (int) $meta['ttl']));
        }
        return $payload;
    } catch (Throwable $e) {
        bao_log_exception($e, 'ajp_warm_stats failed');
        return null;
    }
}

/**
 * Map legacy TipOracle-style market URLs / page keys → api-pages.php keys.
 */
function ajp_api_page_key(string $hint): string
{
    $hint = strtolower(trim($hint));
    $hint = preg_replace('#^/api/#', '', $hint) ?? $hint;
    $hint = trim($hint, '/');

    static $map = [
        'today-predictions-api' => 'football-predictions-today',
        'homepage-api' => 'homepage',
        'btts-predictions-api' => 'btts-predictions',
        '1x2-predictions-api' => '1x2-predictions',
        'double-chance-predictions-api' => 'double-chance-predictions',
        'over-under-predictions-api' => 'over-under-predictions',
        'under-25-35-predictions-api' => 'over-under-predictions',
        'ht-ft-predictions-api' => 'ht-ft-predictions',
        'accumulator-tips-api' => 'accumulator-tips',
        'weekend-accumulators-api' => 'weekend-football-predictions',
        'correct-score-predictions' => '1x2-predictions',
        'top-5-betting-tips-api' => 'sure-bets-today',
        'vip-matches' => 'must-win-teams-today',
        'predictions-today' => 'football-predictions-today',
        'predictions-tomorrow' => 'football-predictions-tomorrow',
        'predictions-weekend' => 'weekend-football-predictions',
        'btts-tips' => 'btts-predictions',
        '1x2-prediction' => '1x2-predictions',
        'double-chance-tips' => 'double-chance-predictions',
        'over-2-5-goals' => 'over-under-predictions',
        'over-1-5-goals' => 'over-under-predictions',
        'over-3-5-goals' => 'over-under-predictions',
        'under-2-5-goals' => 'over-under-predictions',
        'under-3-5-goals' => 'over-under-predictions',
        'ht-ft-predictions' => 'ht-ft-predictions',
        'half-time-predictions' => 'ht-ft-predictions',
        'accumulator-tips' => 'accumulator-tips',
        'must-win-teams-today' => 'must-win-teams-today',
        'jackpot-predictions' => 'jackpot-predictions',
        'sportpesa-mega-jackpot-predictions' => 'sportpesa-mega-jackpot-predictions',
        'sportpesa-midweek-jackpot-predictions' => 'sportpesa-midweek-jackpot-predictions',
        'betika-midweek-jackpot-predictions' => 'betika-midweek-jackpot-predictions',
        'mozzart-super-daily-jackpot-predictions' => 'mozzart-super-daily-jackpot-predictions',
        'odibets-laki-tatu-predictions' => 'odibets-laki-tatu-predictions',
        'odibet-laki-tatu-daily-jackpot-predictions' => 'odibets-laki-tatu-predictions',
        'sportybet-jackpot-predictions' => 'sportybet-daily-jackpot-predictions',
    ];

    return $map[$hint] ?? $hint;
}

/**
 * Convert a Bao game row into TipOracle-compatible fixture shape used by AJP pages.
 *
 * @param array<string,mixed> $g
 * @return array<string,mixed>
 */
function ajp_game_to_tiporacle(array $g): array
{
    $pick = (string) ($g['pick'] ?? $g['pick_code'] ?? 'Home');
    $code = (string) ($g['pick_code'] ?? '');
    $conf = (int) ($g['confidence'] ?? 50);
    $odd = $g['odds'] ?? null;
    if (is_string($odd) && $odd !== '' && is_numeric($odd)) {
        $odd = (float) $odd;
    } elseif (!is_numeric($odd)) {
        $odd = null;
    }

    $kickoff = (string) ($g['kickoff'] ?? $g['kickoff_iso'] ?? '');
    $dateIso = $kickoff !== '' ? $kickoff : (string) ($g['date'] ?? '');

    $leagueName = (string) ($g['league'] ?? 'Football');
    // Strip "#N · " jackpot position prefix for tip pages
    $leagueName = preg_replace('/^#\d+\s*·\s*/', '', $leagueName) ?? $leagueName;

    $predLabel = $pick;
    if ($code === '1') {
        $predLabel = 'Home Win';
    } elseif ($code === '2') {
        $predLabel = 'Away Win';
    } elseif ($code === 'X') {
        $predLabel = 'Draw';
    } elseif ($code === '1X') {
        $predLabel = 'Home or Draw';
    } elseif ($code === 'X2') {
        $predLabel = 'Draw or Away';
    } elseif ($code === '12') {
        $predLabel = 'Home or Away';
    }

    $market = strtolower((string) ($g['market'] ?? '1x2'));
    if ($market === 'btts') {
        $predLabel = stripos($pick, 'no') !== false ? 'BTTS No' : 'BTTS Yes';
    } elseif ($market === 'over_under' || $market === 'over/under') {
        $predLabel = $pick;
    }

    $status = strtoupper((string) ($g['status'] ?? 'NS'));
    $score = $g['score'] ?? null;
    $goalsHome = null;
    $goalsAway = null;
    if (is_string($score) && str_contains($score, '-')) {
        [$goalsHome, $goalsAway] = array_map('intval', explode('-', $score, 2));
    }

    $scores = [
        'display' => is_string($score) ? $score : ((isset($goalsHome, $goalsAway)) ? ($goalsHome . '-' . $goalsAway) : null),
        'home' => $goalsHome,
        'away' => $goalsAway,
        'full_display' => is_string($score) ? $score : null,
    ];

    return [
        'fixture_id' => $g['fixture_id'] ?? $g['id'] ?? null,
        'date' => $dateIso,
        'date_label' => (string) ($g['date_label'] ?? ''),
        'status' => $status,
        'status_short' => $status,
        'goals_home' => $goalsHome,
        'goals_away' => $goalsAway,
        'score' => $score,
        'scores' => $scores,
        'home_team' => [
            'name' => (string) ($g['home'] ?? 'Home'),
            'logo' => (string) ($g['home_logo'] ?? ''),
        ],
        'away_team' => [
            'name' => (string) ($g['away'] ?? 'Away'),
            'logo' => (string) ($g['away_logo'] ?? ''),
        ],
        'league' => [
            'name' => $leagueName,
            'country' => (string) ($g['country'] ?? ''),
        ],
        'prediction' => [
            'prediction' => $predLabel,
            'full_prediction' => $predLabel,
            'confidence' => $conf,
            'tip' => $code !== '' ? $code : $pick,
            'type' => $market === '1x2' ? '1X2' : strtoupper(str_replace('_', '/', $market)),
            'odds' => $odd,
            'reason' => (string) ($g['reason'] ?? ''),
        ],
        'odds' => [
            'prediction_odd' => $odd,
            'home' => $g['odds_home'] ?? null,
            'draw' => $g['odds_draw'] ?? null,
            'away' => $g['odds_away'] ?? null,
        ],
        // Flat aliases used by some TipOracle pages
        'home_team_name' => (string) ($g['home'] ?? 'Home'),
        'away_team_name' => (string) ($g['away'] ?? 'Away'),
        'league_name' => $leagueName,
        'tip' => $code !== '' ? $code : $pick,
        'percent_pred_home' => $g['percent_pred_home'] ?? null,
        'percent_pred_draw' => $g['percent_pred_draw'] ?? null,
        'percent_pred_away' => $g['percent_pred_away'] ?? null,
        'won' => $g['won'] ?? null,
        'jackpot_position' => isset($g['league']) && preg_match('/^#(\d+)/', (string) $g['league'], $m)
            ? (int) $m[1]
            : ($g['jackpot_position'] ?? null),
        'jackpot_name' => (string) ($g['jackpot_name'] ?? ''),
        'jackpot_tips_id' => (string) ($g['jackpot_tips_id'] ?? ''),
        'bets_home' => $g['odds_home'] ?? null,
        'bets_draw' => $g['odds_draw'] ?? null,
        'bets_away' => $g['odds_away'] ?? null,
        'pick_code' => $code,
        'confidence' => $conf,
        'market' => $market,
        '_raw' => $g,
    ];
}

/**
 * Fetch fixtures for a tips page key (TipOracle-compatible list).
 *
 * @param array<string,mixed> $opts optional: market_line, under, cache_only, allow_stale
 * @return array{ok:bool,fixtures:list<array>,payload:?array,error:?string}
 */
function ajp_fetch_fixtures(string $pageHint, array $opts = []): array
{
    $key = ajp_api_page_key($pageHint);
    $curlOpts = [];
    if (!empty($opts['cache_only'])) {
        $curlOpts['cache_only'] = true;
    }
    if (array_key_exists('allow_stale', $opts)) {
        $curlOpts['allow_stale'] = (bool) $opts['allow_stale'];
    }
    $payload = ajp_curl_api('/api/' . $key, $curlOpts);
    if ($payload === null) {
        return ['ok' => false, 'fixtures' => [], 'payload' => null, 'error' => 'Predictions temporarily unavailable.'];
    }
    if (($payload['ok'] ?? true) === false) {
        return ['ok' => false, 'fixtures' => [], 'payload' => $payload, 'error' => (string) ($payload['error'] ?? 'No data')];
    }

    $games = $payload['games'] ?? [];
    if (!is_array($games)) {
        $games = [];
    }

    // Optional O/U line filter when page shares over-under-predictions key
    $line = isset($opts['market_line']) ? (string) $opts['market_line'] : '';
    $wantUnder = !empty($opts['under']);
    if ($line !== '' || $wantUnder) {
        $games = array_values(array_filter($games, static function ($g) use ($line, $wantUnder) {
            if (!is_array($g)) {
                return false;
            }
            $pick = strtolower((string) ($g['pick'] ?? $g['pick_code'] ?? ''));
            $market = strtolower((string) ($g['market'] ?? ''));
            if ($wantUnder && !str_contains($pick, 'under') && !str_starts_with($pick, 'u')) {
                return false;
            }
            if (!$wantUnder && $line !== '' && str_contains($pick, 'under')) {
                // over pages: prefer Over picks
                if (!str_contains($pick, 'over') && !str_starts_with($pick, 'o')) {
                    return false;
                }
            }
            if ($line !== '') {
                $needle = str_replace('0', '', $line); // 25 → look for 2.5
                if (strlen($line) === 2) {
                    $needle = $line[0] . '.' . $line[1];
                }
                if ($needle !== '' && !str_contains($pick, $needle) && !str_contains($pick, $line)) {
                    // keep if market is generic over_under without line in pick
                    if ($market !== 'over_under' && $market !== 'over/under') {
                        return false;
                    }
                }
            }
            return true;
        }));
    }

    $fixtures = [];
    foreach ($games as $g) {
        if (is_array($g)) {
            $fixtures[] = ajp_game_to_tiporacle($g);
        }
    }

    return ['ok' => true, 'fixtures' => $fixtures, 'payload' => $payload, 'error' => null];
}

/**
 * Jackpot sheet via selections (Bao-style).
 *
 * @return array{ok:bool,games:list<array>,fixtures:list<array>,payload:?array,count:int,error:?string}
 */
function ajp_fetch_jackpot_sheet(string $slug): array
{
    $key = ajp_api_page_key($slug);
    $payload = ajp_curl_api('/api/' . $key);
    if ($payload === null) {
        return ['ok' => false, 'games' => [], 'fixtures' => [], 'payload' => null, 'count' => 0, 'error' => 'unavailable'];
    }
    $games = is_array($payload['games'] ?? null) ? $payload['games'] : [];
    $fixtures = [];
    foreach ($games as $g) {
        if (!is_array($g)) {
            continue;
        }
        $t = ajp_game_to_tiporacle($g);
        // Enrich for jackpot-page renderer (commercial API field names)
        $raw = $g;
        $t['jackpot_position'] = $t['jackpot_position']
            ?? (isset($raw['league']) && preg_match('/^#(\d+)/', (string) $raw['league'], $m) ? (int) $m[1] : count($fixtures) + 1);
        $t['home_team_name'] = $t['home_team']['name'];
        $t['away_team_name'] = $t['away_team']['name'];
        $t['league_name'] = $t['league']['name'];
        $t['bets_home'] = $t['odds']['home'] ?? $t['odds']['prediction_odd'] ?? '—';
        $t['bets_draw'] = $t['odds']['draw'] ?? '—';
        $t['bets_away'] = $t['odds']['away'] ?? '—';
        $t['percent_pred_home'] = $raw['home_prob'] ?? $t['confidence'];
        $t['percent_pred_draw'] = $raw['draw_prob'] ?? 0;
        $t['percent_pred_away'] = $raw['away_prob'] ?? 0;
        // Align tip % to pick
        $code = (string) ($t['pick_code'] ?? '');
        if ($code === '1') {
            $t['percent_pred_home'] = $t['confidence'];
        } elseif ($code === 'X') {
            $t['percent_pred_draw'] = $t['confidence'];
        } elseif ($code === '2') {
            $t['percent_pred_away'] = $t['confidence'];
        }
        $t['tip'] = $code !== '' ? $code : ($t['prediction']['tip'] ?? '1');
        $t['date'] = $raw['kickoff'] ?? $t['date'];
        $fixtures[] = $t;
    }

    return [
        'ok' => true,
        'games' => $games,
        'fixtures' => $fixtures,
        'payload' => $payload,
        'count' => count($fixtures),
        'error' => null,
    ];
}

function ajp_api_stats(): ?array
{
    static $cached = false;
    static $payload = null;
    if ($cached) {
        return $payload;
    }
    $cached = true;
    $payload = ajp_curl_api('/api/stats');
    return $payload;
}
