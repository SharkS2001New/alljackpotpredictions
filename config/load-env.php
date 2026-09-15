<?php
/**
 * Load environment into putenv / $_ENV (once per request).
 *
 * Priority (never override real process env from k8s flat keys / docker -e):
 *  1. Existing getenv / $_ENV (Helm extraEnv, flat secret keys, redisEnv)
 *  2. Multiline secret injected as env var named ".env" (common laravel-env shape)
 *  3. Project-root `.env` file (local, or volume-mounted from Secret)
 *
 * Docker images must NOT bake secrets; use k8s Secret + this loader.
 */
$root = dirname(__DIR__);
$envFile = $root . '/.env';

if (!function_exists('bao_env_parse_lines')) {
    /**
     * @param list<string> $lines
     */
    function bao_env_parse_lines(array $lines): void
    {
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\"'");
            if ($key === '' || $key === '.env') {
                continue;
            }
            // Never override real process env (flat k8s keys win).
            if (getenv($key) !== false) {
                continue;
            }
            if (array_key_exists($key, $_ENV) && $_ENV[$key] !== '' && $_ENV[$key] !== null) {
                continue;
            }
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

if (empty($GLOBALS['__bao_env_loaded'])) {
    // A) Secret shape: envFrom on a key literally named ".env" (entire file as one var).
    $envBlob = $_ENV['.env'] ?? getenv('.env');
    if (is_string($envBlob) && $envBlob !== '' && str_contains($envBlob, '=')) {
        bao_env_parse_lines(preg_split("/\r\n|\n|\r/", $envBlob) ?: []);
    }

    // B) File: local `.env` or mounted Secret subPath.
    $loadDotenv = true;
    $flag = $_ENV['BAO_LOAD_DOTENV'] ?? getenv('BAO_LOAD_DOTENV');
    if ($flag !== false && $flag !== null && $flag !== '') {
        $loadDotenv = !in_array(strtolower((string) $flag), ['0', 'false', 'no', 'off'], true);
    }
    if ($loadDotenv && is_file($envFile) && is_readable($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES);
        if (is_array($lines)) {
            bao_env_parse_lines($lines);
        }
    }

    $GLOBALS['__bao_env_loaded'] = true;
}

if (!function_exists('bao_env')) {
    function bao_env(string $key, mixed $default = null): mixed
    {
        $v = $_ENV[$key] ?? getenv($key);
        return ($v === false || $v === null || $v === '') ? $default : $v;
    }
}
