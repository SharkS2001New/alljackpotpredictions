<?php
namespace App\Support;

use DateInterval;
use DateTimeInterface;
use Predis\Client;
use Throwable;

/**
 * Laravel-style Cache facade (Predis redis store + file store).
 * Same env contract as pitchpredictionsbackend: CACHE_DRIVER, REDIS_*, CACHE_PREFIX.
 */
final class Cache
{
    private const JSON_CACHE_SUFFIX = '_json_v2';
    private const INTERNAL_CACHE_SUFFIX = '_internal_v1';

    private static ?self $instance = null;

    /** @var array<string,mixed> */
    private array $cacheCfg;

    /** @var array<string,mixed> */
    private array $redisCfg;

    private static ?Client $redis = null;
    private static bool $redisTried = false;
    private static bool $redisOk = false;
    private static ?string $activeDriver = null;

    private function __construct()
    {
        $this->cacheCfg = require dirname(__DIR__, 2) . '/config/cache.php';
        $this->redisCfg = require dirname(__DIR__, 2) . '/config/redis.php';
    }

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::instance()->storeGet($key);
        return $value !== null ? $value : $default;
    }

    public static function put(string $key, mixed $value, DateTimeInterface|DateInterval|int $ttl): bool
    {
        if ($value === null) {
            return false;
        }
        self::instance()->storePut($key, $value, self::ttlSeconds($ttl));
        return true;
    }

    public static function forget(string $key): bool
    {
        self::instance()->storeForget($key);
        return true;
    }

    /**
     * @template T
     * @param callable():T $callback
     * @return T
     */
    public static function remember(string $key, DateTimeInterface|DateInterval|int $ttl, callable $callback): mixed
    {
        $hit = self::get($key);
        if ($hit !== null) {
            return $hit;
        }
        $value = $callback();
        if ($value !== null) {
            self::put($key, $value, $ttl);
        }
        return $value;
    }

    /** Laravel FixtureApiHelpers-style JSON payload cache key. */
    public static function jsonKey(string $key): string
    {
        return $key . self::JSON_CACHE_SUFFIX;
    }

    public static function internalKey(string $key): string
    {
        return $key . self::INTERNAL_CACHE_SUFFIX;
    }

    public static function getCachedJsonPayload(string $cacheKey, bool $allowStale = false): ?array
    {
        $cached = self::instance()->storeGet(self::jsonKey($cacheKey), $allowStale);
        return is_array($cached) ? $cached : null;
    }

    public static function putCachedJsonPayload(string $cacheKey, array $payload, DateTimeInterface|DateInterval|int $ttl): void
    {
        if ($payload === []) {
            return;
        }
        self::put(self::jsonKey($cacheKey), $payload, $ttl);
    }

    public static function forgetCachedJsonPayload(string $cacheKey): void
    {
        self::forget(self::jsonKey($cacheKey));
    }

    /**
     * Pitch FixtureApiHelpers::fixtureCacheTtlForDate — same bands:
     * past → 12h, today → 10m, tomorrow → 1h, else → 12h.
     * Optional env: CACHE_TTL_TODAY / CACHE_TTL_TOMORROW / CACHE_TTL_PAST (seconds).
     * Do NOT use a blanket CACHE_TTL_PAGE — that broke date-based rules.
     */
    public static function ttlForSiteDate(string $siteDate): int
    {
        $today = DateTimeHelper::siteToday();
        $tomorrow = DateTimeHelper::siteDate('tomorrow');

        if ($siteDate === $today) {
            return self::envTtl('CACHE_TTL_TODAY') ?? (10 * 60);
        }
        if ($siteDate === $tomorrow) {
            return self::envTtl('CACHE_TTL_TOMORROW') ?? (60 * 60);
        }
        // Past or further future — pitch uses 12h either side of today/tomorrow
        return self::envTtl('CACHE_TTL_PAST') ?? (12 * 60 * 60);
    }

    /** Weekend board — pitch weekend endpoint uses 5 hours. */
    public static function ttlWeekend(): int
    {
        return self::envTtl('CACHE_TTL_WEEKEND') ?? (5 * 60 * 60);
    }

    /** Jackpot / selections sheets — pitch often uses 5–10 minutes. */
    public static function ttlJackpot(): int
    {
        return self::envTtl('CACHE_TTL_JACKPOT') ?? (10 * 60);
    }

    /** Stats board — short refresh (not a full-day cache). */
    public static function ttlStats(): int
    {
        // AJP: default 15m — header ticker hits this on every page.
        return self::envTtl('CACHE_TTL_STATS') ?? (15 * 60);
    }

    /**
     * Live tips — pitch caches ~1 minute; we default to no cache (scores change constantly).
     * Set CACHE_TTL_LIVE=60 to match pitch's 1-minute live cache, or any seconds > 0.
     * CACHE_TTL_LIVE=0 or unset → skip cache.
     */
    public static function ttlLive(): int
    {
        if (!function_exists('bao_env')) {
            require_once dirname(__DIR__, 2) . '/config/load-env.php';
        }
        $raw = bao_env('CACHE_TTL_LIVE');
        if ($raw === null || $raw === '') {
            return 0; // no cache by default
        }
        $n = (int) $raw;
        return max(0, $n);
    }

    private static function envTtl(string $key): ?int
    {
        if (!function_exists('bao_env')) {
            require_once dirname(__DIR__, 2) . '/config/load-env.php';
        }
        $raw = bao_env($key);
        if ($raw === null || $raw === '') {
            return null;
        }
        $n = (int) $raw;
        return $n > 0 ? $n : null;
    }

    public static function driver(): string
    {
        self::instance()->resolveDriver();
        return self::$activeDriver ?? 'file';
    }

    private function resolveDriver(): string
    {
        if (self::$activeDriver !== null) {
            return self::$activeDriver;
        }
        $wanted = strtolower((string) ($this->cacheCfg['default'] ?? 'file'));
        if ($wanted === 'redis' && $this->redisClient() !== null) {
            self::$activeDriver = 'redis';
        } else {
            self::$activeDriver = 'file';
        }
        return self::$activeDriver;
    }

    private function storeGet(string $key, bool $allowStale = false): mixed
    {
        $full = $this->prefixed($key);
        if ($this->resolveDriver() === 'redis') {
            try {
                $raw = $this->redisClient()?->get($full);
                if (is_string($raw) && $raw !== '') {
                    return $this->unserialize($raw);
                }
                // Redis TTL already dropped expired keys — try file stale sibling if needed
                if ($allowStale) {
                    return $this->fileGet($full, true);
                }
                return null;
            } catch (Throwable $e) {
                self::$redisOk = false;
                self::$activeDriver = 'file';
                self::logRedisFallback($e, 'get');
            }
        }
        return $this->fileGet($full, $allowStale);
    }

    private function storePut(string $key, mixed $value, int $seconds): void
    {
        $full = $this->prefixed($key);
        $seconds = max(1, $seconds);
        $payload = $this->serialize($value);

        if ($this->resolveDriver() === 'redis') {
            try {
                $this->redisClient()?->setex($full, $seconds, $payload);
                return;
            } catch (Throwable $e) {
                self::$redisOk = false;
                self::$activeDriver = 'file';
                self::logRedisFallback($e, 'put');
            }
        }
        $this->filePut($full, $value, $seconds);
    }

    private function storeForget(string $key): void
    {
        $full = $this->prefixed($key);
        if ($this->resolveDriver() === 'redis') {
            try {
                $this->redisClient()?->del([$full]);
            } catch (Throwable $e) {
                self::$redisOk = false;
                self::$activeDriver = 'file';
                self::logRedisFallback($e, 'forget');
            }
        }
        $path = $this->filePath($full);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function redisClient(): ?Client
    {
        if (self::$redisTried) {
            return self::$redisOk ? self::$redis : null;
        }
        self::$redisTried = true;

        if (!class_exists(Client::class)) {
            return null;
        }

        try {
            $conn = $this->redisCfg['cache'] ?? $this->redisCfg['default'] ?? [];
            $url = trim((string) ($conn['url'] ?? ''));
            if ($url !== '') {
                $params = $url;
            } else {
                $params = [
                    'scheme' => 'tcp',
                    'host' => (string) ($conn['host'] ?? 'redis'),
                    'port' => (int) ($conn['port'] ?? 6379),
                    'database' => (int) ($conn['database'] ?? 1),
                    'timeout' => (float) ($conn['timeout'] ?? 1.5),
                    'read_write_timeout' => (float) ($conn['read_write_timeout'] ?? 1.5),
                ];
                $password = $conn['password'] ?? null;
                if ($password !== null && $password !== '') {
                    $params['password'] = (string) $password;
                }
                $username = $conn['username'] ?? null;
                if ($username !== null && $username !== '') {
                    $params['username'] = (string) $username;
                }
            }

            $client = new Client($params, [
                'prefix' => (string) ($this->redisCfg['options']['prefix'] ?? 'pitch_predictions_database_'),
            ]);
            $client->ping();
            self::$redis = $client;
            self::$redisOk = true;
            return $client;
        } catch (Throwable $e) {
            self::$redis = null;
            self::$redisOk = false;
            self::logRedisFallback($e, 'connect');
            return null;
        }
    }

    private static function logRedisFallback(Throwable $e, string $op): void
    {
        static $logged = [];
        if (isset($logged[$op])) {
            return;
        }
        $logged[$op] = true;
        if (function_exists('bao_log_exception')) {
            bao_log_exception($e, 'Redis unavailable; falling back to file cache', ['op' => $op]);
        } else {
            error_log('[bao] Redis ' . $op . ' failed: ' . $e->getMessage());
        }
    }

    /**
     * Laravel RedisStore::setPrefix — cache prefix is always followed by ":".
     * Full Redis key = REDIS_PREFIX + CACHE_PREFIX + ":" + key
     * e.g. pitch_predictions_database_pitch_predictions_cache_:todays_football_fixtures_…_json_v2
     */
    private function prefixed(string $key): string
    {
        $prefix = (string) ($this->cacheCfg['prefix'] ?? 'pitch_predictions_cache_');
        if ($prefix !== '' && !str_ends_with($prefix, ':')) {
            $prefix .= ':';
        }
        return $prefix . $key;
    }

    private static function ttlSeconds(DateTimeInterface|DateInterval|int $ttl): int
    {
        if (is_int($ttl)) {
            return $ttl;
        }
        if ($ttl instanceof DateInterval) {
            $now = new \DateTimeImmutable('now');
            return max(1, $now->add($ttl)->getTimestamp() - $now->getTimestamp());
        }
        return max(1, $ttl->getTimestamp() - time());
    }

    private function serialize(mixed $value): string
    {
        return serialize($value);
    }

    private function unserialize(string $raw): mixed
    {
        try {
            return unserialize($raw, ['allowed_classes' => false]);
        } catch (Throwable $e) {
            // Legacy JSON entries from earlier Bao cache format.
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : null;
        }
    }

    private function fileGet(string $fullKey, bool $allowStale = false): mixed
    {
        $path = $this->filePath($fullKey);
        if (!is_file($path)) {
            return null;
        }
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return null;
        }
        $wrap = @unserialize($raw, ['allowed_classes' => false]);
        if (!is_array($wrap) || !isset($wrap['expires'], $wrap['data'])) {
            return null;
        }
        if ((int) $wrap['expires'] < time()) {
            if ($allowStale) {
                return $wrap['data'];
            }
            @unlink($path);
            return null;
        }
        return $wrap['data'];
    }

    private function filePut(string $fullKey, mixed $value, int $seconds): void
    {
        $dir = $this->fileDir();
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $path = $this->filePath($fullKey);
        $wrap = serialize([
            'expires' => time() + $seconds,
            'data' => $value,
        ]);
        @file_put_contents($path, $wrap, LOCK_EX);
    }

    private function fileDir(): string
    {
        $stores = $this->cacheCfg['stores']['file']['path']
            ?? (dirname(__DIR__, 2) . '/storage/framework/cache/data');
        return rtrim((string) $stores, '/');
    }

    private function filePath(string $fullKey): string
    {
        return $this->fileDir() . '/' . sha1($fullKey) . '.cache';
    }
}
