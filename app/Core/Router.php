<?php
/**
 * app/Core/Router.php  -  maps URL + HTTP method to Controller@method (Core lead only)
 * Middleware and {id} parameters are the next step; this version handles plain routes.
 */
class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, string $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Strip the folder the app lives in (e.g. /studycircle/public) and index.php
        $base = base_url();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim($path, '/');
        if ($path === '/index.php') {
            $path = '/';
        }

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 - Page not found';   // TODO: render views/errors/404.php
            return;
        }

        [$class, $action] = explode('@', $handler);
        (new $class())->$action();
    }
}
