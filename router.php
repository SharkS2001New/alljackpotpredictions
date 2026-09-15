<?php
/**
 * Front controller for the PHP built-in server (Bao Predictions pattern).
 *
 * Start from project root (recommended):
 *   ./serve 5001
 *   php -S 127.0.0.1:5001 -t . router.php
 *
 * Apache uses .htaccess instead — this file is for `php -S` only.
 *
 * Flow:
 *   1) Pass through real static files (return false → docroot via -t)
 *   2) Special redirects / API / jackpots
 *   3) Clean URL → matching .php / .html on disk
 */
declare(strict_types=1);

$root = __DIR__;

$rawUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$path = parse_url($rawUri, PHP_URL_PATH);
$uri = is_string($path) ? urldecode($path) : '/';
if ($uri === '' || $uri === false) {
    $uri = '/';
}
// Normalize: always leading slash, no trailing slash (except root)
if ($uri[0] !== '/') {
    $uri = '/' . $uri;
}
if ($uri !== '/') {
    $uri = rtrim($uri, '/') ?: '/';
}

// Keep a normalized path available to included pages / API
$query = parse_url($rawUri, PHP_URL_QUERY);
$_SERVER['REQUEST_URI'] = $uri . (is_string($query) && $query !== '' ? ('?' . $query) : '');

/**
 * Serve an on-disk PHP page and stop routing.
 */
$servePhp = static function (string $absolutePath) use ($root): bool {
    if (!is_file($absolutePath)) {
        return false;
    }
    // Align SCRIPT_* with the page being served (helps includes / diagnostics)
    $_SERVER['SCRIPT_FILENAME'] = $absolutePath;
    $_SERVER['SCRIPT_NAME'] = '/' . ltrim(str_replace('\\', '/', substr($absolutePath, strlen($root))), '/');
    // Prevent homepage re-dispatch when router includes index.php for "/"
    if (basename($absolutePath) === 'index.php' && !defined('AJP_AS_PAGE')) {
        define('AJP_AS_PAGE', true);
    }
    require $absolutePath;
    return true;
};

// ── 1) Real static files / explicit .php paths under docroot ───────────
if ($uri !== '/') {
    $file = $root . $uri;
    if (is_file($file) && !is_dir($file)) {
        // Let the built-in server stream CSS/JS/images/etc.
        // (Also allows /path/to/file.php to run as a normal script.)
        return false;
    }
}

// ── 2) Special routes ──────────────────────────────────────────────────
if ($uri === '/oracle-picks-today') {
    header('Location: /jackpot-picks-today', true, 301);
    exit;
}
if ($uri === '/jackpots' || $uri === '/jackpot-predictions') {
    header('Location: /jackpot-picks-today', true, $uri === '/jackpots' ? 302 : 301);
    exit;
}
if (preg_match('#^/jackpot-predictions/([a-z0-9\-]+)$#', $uri, $m)) {
    header('Location: /jackpots/' . $m[1], true, 301);
    exit;
}
if (preg_match('#^/api/([a-z0-9\-]+)$#', $uri)) {
    if ($servePhp($root . '/api/index.php')) {
        return true;
    }
}
if (preg_match('#^/jackpots/([a-z0-9\-]+)$#', $uri, $m)) {
    $slug = $m[1];
    // Files live in /jackpot-pages/ so the URL prefix /jackpots/ is not a real
    // directory (PHP built-in server 404s paths under existing dirs without a router).
    $dedicated = $root . '/jackpot-pages/' . $slug . '.php';
    if (is_file($dedicated)) {
        $servePhp($dedicated);
        return true;
    }
    $_GET['slug'] = $slug;
    if ($servePhp($root . '/jackpot-predictions.php')) {
        return true;
    }
}

// ── 3) Homepage ────────────────────────────────────────────────────────
if ($uri === '/') {
    if ($servePhp($root . '/index.php')) {
        return true;
    }
}

// ── 4) Clean URL → {slug}.php / {slug}.html ─────────────────────────────
$php = $root . $uri . '.php';
if ($servePhp($php)) {
    return true;
}
$html = $root . $uri . '.html';
if (is_file($html)) {
    header('Content-Type: text/html; charset=UTF-8');
    readfile($html);
    return true;
}

// ── 5) 404 ─────────────────────────────────────────────────────────────
http_response_code(404);
if (is_file($root . '/404.php')) {
    $servePhp($root . '/404.php');
} else {
    echo '404 Not Found';
}
return true;
