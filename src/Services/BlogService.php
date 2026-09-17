<?php

namespace App\Services;

use App\Support\Cache;

/**
 * Fetch AllJackpotPredictions blogs from the shared pitchpredictions API (website=ajp).
 */
final class BlogService
{
    public const SITE_KEY = 'ajp';

    private const LIST_CACHE_PREFIX = 'ajp_blog_list_v1_';
    private const POST_CACHE_PREFIX = 'ajp_blog_post_v1_';

    /**
     * @return array{data: list<array<string,mixed>>, meta?: array<string,mixed>, links?: array<string,mixed>}
     */
    public function list(int $page = 1, string $category = 'ALL', int $perPage = 20): array
    {
        $page = max(1, $page);
        $category = trim($category) !== '' ? trim($category) : 'ALL';
        $perPage = min(max(1, $perPage), 50);
        $cacheKey = self::LIST_CACHE_PREFIX . "{$category}_p{$page}_n{$perPage}";

        $cached = Cache::getCachedJsonPayload($cacheKey);
        if (is_array($cached) && isset($cached['data']) && is_array($cached['data'])) {
            return $cached;
        }

        $query = http_build_query([
            'site' => self::SITE_KEY,
            'page' => $page,
            'category' => $category,
            'per_page' => $perPage,
        ]);

        $payload = $this->requestJson('/api/blog?' . $query);
        if ($payload === null) {
            return ['data' => []];
        }

        if (!isset($payload['data']) || !is_array($payload['data'])) {
            $payload = ['data' => is_array($payload) ? array_values($payload) : []];
        }

        Cache::putCachedJsonPayload($cacheKey, $payload, 15 * 60);

        return $payload;
    }

    /**
     * @return array<string,mixed>|null
     */
    public function post(string $slug): ?array
    {
        $slug = trim($slug);
        if ($slug === '' || !preg_match('/^[a-z0-9][a-z0-9\-]{0,190}$/i', $slug)) {
            return null;
        }

        $cacheKey = self::POST_CACHE_PREFIX . strtolower($slug);
        $cached = Cache::getCachedJsonPayload($cacheKey);
        if (is_array($cached) && (($cached['slug'] ?? '') !== '' || ($cached['title'] ?? '') !== '')) {
            return $cached;
        }

        $payload = $this->requestJson('/api/blog/' . rawurlencode($slug) . '?site=' . self::SITE_KEY);
        if ($payload === null) {
            return null;
        }

        if (isset($payload['data']) && is_array($payload['data']) && isset($payload['data']['slug'])) {
            $payload = $payload['data'];
        }

        if (($payload['slug'] ?? '') === '' && ($payload['title'] ?? '') === '') {
            return null;
        }

        Cache::putCachedJsonPayload($cacheKey, $payload, 6 * 3600);

        return $payload;
    }

    /**
     * @return array{ok: bool, cleared: list<string>}
     */
    public function clearListCaches(): array
    {
        $cleared = [];
        foreach (['ALL'] as $category) {
            foreach ([1, 2, 3] as $page) {
                foreach ([6, 8, 10, 12, 20, 50] as $perPage) {
                    $key = self::LIST_CACHE_PREFIX . "{$category}_p{$page}_n{$perPage}";
                    Cache::forgetCachedJsonPayload($key);
                    $cleared[] = $key;
                }
            }
        }

        return ['ok' => true, 'cleared' => $cleared];
    }

    /**
     * @return array{ok: bool, cleared: list<string>}
     */
    public function clearPostCache(string $slug): array
    {
        $slug = trim($slug);
        $key = self::POST_CACHE_PREFIX . strtolower($slug);
        Cache::forgetCachedJsonPayload($key);

        return ['ok' => true, 'cleared' => [$key]];
    }

    /**
     * @return array<string,mixed>|null
     */
    private function requestJson(string $pathAndQuery): ?array
    {
        if (!function_exists('bao_env')) {
            require_once dirname(__DIR__, 2) . '/config/load-env.php';
        }

        $base = rtrim((string) bao_env('PITCH_API_URL', 'https://api.pitchpredictions.com'), '/');
        $url = $base . (str_starts_with($pathAndQuery, '/') ? $pathAndQuery : '/' . $pathAndQuery);

        $headers = [
            'Accept: application/json',
            'Origin: https://www.alljackpotpredictions.com',
            'Referer: https://www.alljackpotpredictions.com/',
            'X-Site-Key: ' . self::SITE_KEY,
        ];

        $token = trim((string) bao_env('ACCESS_TOKEN', bao_env('PITCH_ACCESS_TOKEN', '')));
        $token = preg_replace('/^Bearer\s+/i', '', $token) ?? $token;
        if ($token !== '') {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init($url);
        if ($ch === false) {
            return null;
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);

        if ($body === false || $status < 200 || $status >= 300) {
            if (function_exists('bao_log')) {
                bao_log('warning', 'Blog API fetch failed', [
                    'url' => $url,
                    'status' => $status,
                    'error' => $err !== '' ? $err : null,
                ]);
            }

            return null;
        }

        $decoded = json_decode((string) $body, true);

        return is_array($decoded) ? $decoded : null;
    }
}
