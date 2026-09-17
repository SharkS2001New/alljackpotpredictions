<?php
/**
 * Admin blog cache clear + list rewarm endpoints (pitchpredictionsadmin BlogCachePurger).
 *
 *   GET /api/blog-list
 *   GET /api/clear-blog-list-cache
 *   GET /api/clear-blog-cache/{slug}
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/load-env.php';
require_once dirname(__DIR__) . '/src/Support/log.php';
require_once dirname(__DIR__) . '/src/Support/Cache.php';
require_once dirname(__DIR__) . '/src/Services/BlogService.php';
require_once __DIR__ . '/blog-cache-auth.php';
require_once dirname(__DIR__) . '/src/Api/helpers.php';

use App\Services\BlogService;

$path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$path = rawurldecode(rtrim($path, '/') ?: '/');

header('Content-Type: application/json; charset=UTF-8');

if ($path === '/api/blog-list') {
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $category = trim((string) ($_GET['category'] ?? 'ALL'));
    if ($category === '') {
        $category = 'ALL';
    }
    $payload = (new BlogService())->list($page, $category, 6);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($path === '/api/clear-blog-list-cache') {
    if (!ajp_blog_cache_clear_key()) {
        http_response_code(503);
        echo json_encode(['ok' => false, 'error' => 'BLOG_CACHE_CLEAR_KEY not configured']);
        exit;
    }
    if (!ajp_blog_cache_clear_authorized()) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
        exit;
    }
    $cleared = (new BlogService())->clearListCaches();
    echo json_encode($cleared, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (preg_match('#^/api/clear-blog-cache/([a-z0-9][a-z0-9\-]{0,190})$#i', $path, $m)) {
    if (!ajp_blog_cache_clear_key()) {
        http_response_code(503);
        echo json_encode(['ok' => false, 'error' => 'BLOG_CACHE_CLEAR_KEY not configured']);
        exit;
    }
    if (!ajp_blog_cache_clear_authorized()) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
        exit;
    }
    $cleared = (new BlogService())->clearPostCache($m[1]);
    echo json_encode($cleared, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(404);
echo json_encode(['ok' => false, 'error' => 'Not found']);
