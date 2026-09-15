<?php
/**
 * Auth guard. require_once this at the very top of any admin page that
 * should be locked behind login (before any HTML output).
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';

const ADMIN_IDLE_TIMEOUT = 7200; // seconds — 2 hours of inactivity logs you out

if (empty($_SESSION['admin_logged_in'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI'] ?? 'index.php');
    header('Location: login.php?redirect=' . $redirect);
    exit;
}

if (!empty($_SESSION['admin_last_active']) && (time() - $_SESSION['admin_last_active']) > ADMIN_IDLE_TIMEOUT) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['admin_last_active'] = time();
