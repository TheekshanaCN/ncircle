<?php
/**
 * public/index.php  -  FRONT CONTROLLER
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

require BASE_PATH . "/app/Helpers/functions.php";
load_env(BASE_PATH . "/.env");

// Must stay false on the VPS: it controls error details and dev-only pages.
define("APP_DEBUG", filter_var(env_value("APP_DEBUG", false), FILTER_VALIDATE_BOOLEAN));

error_reporting(E_ALL);
ini_set("display_errors", APP_DEBUG ? "1" : "0");
ini_set("log_errors", "1");
date_default_timezone_set((string) config("app.timezone", "Asia/Colombo"));

spl_autoload_register(function (string $class): void {
    // Admin\DashboardController lives in Controllers/Admin (the student one shares its short name).
    if (str_starts_with($class, "Admin\\")) {
        $file = BASE_PATH . "/app/Controllers/Admin/" . substr($class, 6) . ".php";
        if (is_file($file)) {
            require $file;
        }
        return;
    }
    $class = basename(str_replace("\\", "/", $class));
    foreach (
        [
            "Core",
            "Controllers",
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

set_exception_handler(function (Throwable $e): void {
    error_log((string) $e);
    Response::error(500, APP_DEBUG ? $e : null);
});

Response::securityHeaders();
Session::start();
Auth::restoreRemembered();

$router = new Router();
require BASE_PATH . "/routes/web.php";
$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
