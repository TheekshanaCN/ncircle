<?php
/**
 * app/Core/Router.php  -  maps URL + HTTP method to Controller@method.
 *   $router->get('/resources/{id}', 'ResourceController@show', ['auth']);
 * {id} only matches digits and reaches the method as an int.
 * Every POST route is CSRF-checked automatically.
 */
class Router
{
    private array $routes = [];

    private const MIDDLEWARE = [
        'auth'  => AuthMiddleware::class,
        'guest' => GuestMiddleware::class,
        'admin' => AdminMiddleware::class,
    ];

    public function get(string $path, string $handler, array $middleware = []): void
    {
        $this->routes['GET'][$path] = [$handler, $middleware];
    }

    public function post(string $path, string $handler, array $middleware = []): void
    {
        $this->routes['POST'][$path] = [$handler, $middleware];
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

        $method = $method === 'HEAD' ? 'GET' : $method;
        [$route, $params] = $this->match($method, $path);
        if ($route === null) {
            Response::error(404);
        }

        if ($method === 'POST') {
            (new CsrfMiddleware())->handle();
        }
        foreach ($route[1] as $name) {
            $class = self::MIDDLEWARE[$name];
            (new $class())->handle();
        }

        [$class, $action] = explode('@', $route[0]);
        (new $class())->$action(...$params);
    }

    private function match(string $method, string $path): array
    {
        $routes = $this->routes[$method] ?? [];
        if (isset($routes[$path])) {
            return [$routes[$path], []];
        }
        foreach ($routes as $pattern => $route) {
            if (!str_contains($pattern, '{')) {
                continue;
            }
            $regex = '#^' . preg_replace('#\{[a-z_]+\}#', '(\d+)', $pattern) . '$#';
            if (preg_match($regex, $path, $matches)) {
                return [$route, array_map('intval', array_slice($matches, 1))];
            }
        }
        return [null, []];
    }
}
