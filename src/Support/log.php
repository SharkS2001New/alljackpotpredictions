<?php
/**
 * Structured app logging → PHP error_log → container stderr.
 * In k3s: kubectl logs -f deploy/baopredictions -n <ns>
 */

if (!function_exists('bao_log')) {
    /**
     * @param 'debug'|'info'|'warning'|'error'|'critical' $level
     * @param array<string,mixed> $context
     */
    function bao_log(string $level, string $message, array $context = []): void
    {
        $level = strtolower($level);
        $allowed = ['debug', 'info', 'warning', 'error', 'critical'];
        if (!in_array($level, $allowed, true)) {
            $level = 'info';
        }

        // Skip debug noise outside local unless BAO_LOG_DEBUG=1
        if ($level === 'debug') {
            $env = strtolower((string) (($_ENV['APP_ENV'] ?? getenv('APP_ENV')) ?: 'production'));
            $debug = (string) (($_ENV['BAO_LOG_DEBUG'] ?? getenv('BAO_LOG_DEBUG')) ?: '');
            if ($env !== 'local' && $debug !== '1' && strtolower($debug) !== 'true') {
                return;
            }
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        $base = [
            'time' => date('c'),
            'level' => $level,
            'msg' => $message,
        ];
        if ($method !== '' || $uri !== '') {
            $base['request'] = trim($method . ' ' . $uri);
        }
        if ($context !== []) {
            $base['context'] = $context;
        }

        $json = json_encode($base, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = '{"level":"' . $level . '","msg":' . json_encode($message) . '}';
        }
        error_log('[bao] ' . $json);
    }
}

if (!function_exists('bao_log_exception')) {
    function bao_log_exception(Throwable $e, string $message = 'Unhandled exception', array $context = []): void
    {
        bao_log('error', $message, array_merge($context, [
            'exception' => get_class($e),
            'error' => $e->getMessage(),
            'file' => $e->getFile() . ':' . $e->getLine(),
        ]));
    }
}
