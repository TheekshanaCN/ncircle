<?php
/**
 * public/index.php  -  FRONT CONTROLLER (Core lead only)
 * Every request enters here. It never contains page HTML.
 */
declare(strict_types=1);


if (PHP_SAPI === "cli-server") {
    $file = __DIR__ . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

define("BASE_PATH", dirname(__DIR__));
define("APP_DEBUG", true); // TODO (Core lead): read from .env, must be false on the VPS

require BASE_PATH . "/app/Helpers/functions.php";


spl_autoload_register(function (string $class): void {
    $class = basename(str_replace("\\", "/", $class));
    foreach (
        [
            "Core",
            "Controllers",
            "Controllers/Admin",
            "Models",
            "Services",
            "Middleware",
        ]
        as $dir
    ) {
        $file = BASE_PATH . "/app/{$dir}/{$class}.php";
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

// TODO (Core lead): start the session, load .env / config, set error handling.

$router = new Router();
require BASE_PATH . "/routes/web.php";
$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
