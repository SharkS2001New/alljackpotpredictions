<?php
/**
 * JSON API helpers for Bao Predictions.
 */

function bao_api_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function bao_api_error(string $message, int $status = 500, array $extra = []): void
{
    if ($status >= 500) {
        bao_log('error', 'API error', ['status' => $status, 'error' => $message] + $extra);
    } elseif ($status >= 400) {
        bao_log('warning', 'API client error', ['status' => $status, 'error' => $message] + $extra);
    }
    bao_api_json(array_merge([
        'ok' => false,
        'error' => $message,
    ], $extra), $status);
}
