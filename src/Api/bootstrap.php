<?php
/**
 * Shared API bootstrapping for AllJackpotPredictions (Bao-style DB stack).
 */
$root = dirname(__DIR__, 2);
$autoload = $root . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

require_once dirname(__DIR__) . '/Support/log.php';
require_once __DIR__ . '/helpers.php';
require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Support/DateTimeHelper.php';
require_once dirname(__DIR__) . '/Support/Cache.php';
