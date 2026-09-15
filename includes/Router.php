<?php
/**
 * Lightweight front-controller router (same idea as baopredictions App\Facades\Router).
 * Used by router.php for the PHP built-in server.
 */
declare(strict_types=1);

final class AjpRouter
{
    /** @var list<array{method:string,path:string,callback:callable}> */
    private array $routes = [];

    public function get(string $path, callable $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, callable $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    private function addRoute(string $method, string $path, callable $callback): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
        ];
    }

    public function handleRequest(): bool
    {
        $method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $requestUri = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
        $matchMethod = ($method === 'HEAD') ? 'GET' : $method;

        foreach ($this->routes as $route) {
            $params = [];
            if ($matchMethod === $route['method'] && $this->matchPath($route['path'], $requestUri, $params)) {
                if ($method === 'HEAD') {
                    ob_start();
                    call_user_func_array($route['callback'], $params);
                    ob_end_clean();
                    return true;
                }
                call_user_func_array($route['callback'], $params);
                return true;
            }
        }

        return false;
    }

    /** @param list<string> $params */
    private function matchPath(string $routePath, string $requestUri, array &$params): bool
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '@^' . $pattern . '/?$@';

        if (!preg_match($pattern, $requestUri, $matches)) {
            return false;
        }
        array_shift($matches);
        $params = $matches;
        return true;
    }
}
