<?php
/**
 * Public page API: GET /api/{page-key}?start_index=&end_index=&format=html&layout=pt|pcard
 * Bao/Pitch-style window loading for Show More Matches.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/load-more.php';
require_once dirname(__DIR__) . '/src/Api/helpers.php';

$path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$path = rawurldecode($path);

if (!preg_match('#^/api/([a-z0-9\-]+)/?$#', $path, $m)) {
    bao_api_error('Not found', 404);
}

$pageKey = ajp_api_page_key($m[1]);
$pages = require dirname(__DIR__) . '/config/api-pages.php';
if (!isset($pages[$pageKey]) && $pageKey !== 'stats') {
    bao_api_error('Unknown page', 404);
}

if ($pageKey === 'stats') {
    $payload = ajp_api_stats();
    if ($payload === null) {
        bao_api_error('Stats unavailable', 503);
    }
    bao_api_json($payload);
}

$hasWindow = isset($_GET['start_index']) || isset($_GET['end_index']);
$wantHtml = (($_GET['format'] ?? '') === 'html');

// Full payload (cached) when no window — same as Bao.
if (!$hasWindow && !$wantHtml) {
    $payload = ajp_curl_api('/api/' . $pageKey);
    if ($payload === null) {
        bao_api_error('Unavailable', 503);
    }
    bao_api_json($payload);
}

$start = isset($_GET['start_index']) ? max(0, (int) $_GET['start_index']) : 0;
$end = isset($_GET['end_index']) ? max(0, (int) $_GET['end_index']) : ($start + 9);
if ($end < $start) {
    $end = $start;
}

$opts = [
    'league' => isset($_GET['league']) ? (string) $_GET['league'] : '',
    'type' => isset($_GET['type']) ? (string) $_GET['type'] : '',
    'exclude_ft' => isset($_GET['exclude_ft']) && (string) $_GET['exclude_ft'] === '1',
    'market_line' => isset($_GET['market_line']) ? (string) $_GET['market_line'] : '',
    'under' => isset($_GET['under']) && (string) $_GET['under'] === '1',
];

$window = ajp_fixtures_window($pageKey, $start, $end, $opts);
if (!$window['ok']) {
    bao_api_error((string) ($window['error'] ?? 'Unavailable'), 503);
}

$layout = strtolower((string) ($_GET['layout'] ?? 'pcard'));
$marketLabel = (string) ($_GET['market_label'] ?? 'Prediction');

$payload = [
    'ok' => true,
    'page' => $pageKey,
    'count' => $window['count'],
    'games' => array_map(static function ($f) {
        // Prefer raw Bao shape when present for consumers that want it
        return is_array($f['_raw'] ?? null) ? $f['_raw'] : $f;
    }, $window['fixtures']),
    'fixtures' => $window['fixtures'],
    'has_more' => $window['has_more'],
    'next_start' => $window['next_start'],
    'start_index' => $start,
    'end_index' => $start + max(0, $window['count'] - 1),
    'max' => $window['max'],
];

if ($wantHtml) {
    if ($layout === 'pt') {
        $rows = '';
        $cards = '';
        foreach ($window['fixtures'] as $f) {
            if (!is_array($f)) {
                continue;
            }
            $rows .= ajp_render_pt_row($f);
            $cards .= ajp_render_pt_mcard($f);
        }
        $payload['html_rows'] = $rows;
        $payload['html_cards'] = $cards;
        $payload['html'] = $rows;
    } else {
        $html = '';
        foreach ($window['fixtures'] as $f) {
            if (!is_array($f)) {
                continue;
            }
            $html .= ajp_render_pcard($f, ['market_label' => $marketLabel]);
        }
        $payload['html'] = $html;
    }
}

bao_api_json($payload);
