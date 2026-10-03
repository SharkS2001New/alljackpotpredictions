<?php
/**
 * Footer sponsor links API for pitchpredictionsadmin (Bao-compatible).
 *
 *   GET  /api/site-content/footer-sponsors
 *   PUT  /api/site-content/footer-sponsors
 *   POST /api/site-content/footer-sponsors
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/load-env.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/includes/blog-cache-auth.php';

use App\Services\FooterSponsorsService;

header('Content-Type: application/json; charset=utf-8');

$service = new FooterSponsorsService();
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($method === 'GET') {
    $wantAll = in_array(strtolower((string) ($_GET['all'] ?? '')), ['1', 'true'], true);
    if ($wantAll) {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        echo json_encode([
            'success' => true,
            'data' => $service->readDocument(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
    $document = $service->readDocument();
    $links = $service->visibleLinks();
    header('Cache-Control: public, max-age=0, s-maxage=0, must-revalidate');
    echo json_encode([
        'success' => true,
        'updated_at' => $document['updated_at'],
        'links' => $links,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'PUT' || $method === 'POST') {
    if (!ajp_blog_cache_clear_key()) {
        http_response_code(503);
        echo json_encode([
            'error' => 'BLOG_CACHE_CLEAR_KEY must be set to exactly ' . AJP_BLOG_CACHE_CLEAR_KEY_LENGTH . ' characters on this host before footer links can be saved.',
        ]);
        exit;
    }
    if (!ajp_blog_cache_clear_authorized()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $rawBody = file_get_contents('php://input');
    $body = json_decode((string) $rawBody, true);
    if (!is_array($body)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON body.']);
        exit;
    }
    $incoming = (isset($body['data']) && is_array($body['data'])) ? $body['data'] : $body;
    if (!isset($incoming['links']) || !is_array($incoming['links'])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Body must include a links array.']);
        exit;
    }

    try {
        $saved = $service->writeDocument($incoming);
        header('Cache-Control: no-store, no-cache, must-revalidate');
        echo json_encode([
            'success' => true,
            'message' => 'Footer sponsor links saved.',
            'data' => $saved,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage() ?: 'Failed to write footer-sponsors.json',
        ]);
    }
    exit;
}

http_response_code(405);
header('Allow: GET, PUT, POST');
echo json_encode(['error' => 'Method not allowed']);
