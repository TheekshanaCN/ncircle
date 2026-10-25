<?php
/**
 * app/Helpers/functions.php  -  global helpers (Core lead only)
 */

/** Escape output. Use on EVERY value printed in a view. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Folder the app is served from ('' when the docroot is public/). */
function base_url(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    return $dir === '/' || $dir === '.' ? '' : rtrim($dir, '/');
}

/** url('/resources') -> /studycircle/public/resources (works on localhost and the VPS) */
function url(string $path = '/'): string
{
    return base_url() . '/' . ltrim($path, '/');
}

/** asset('css/theme.css') -> /<base>/assets/css/theme.css */
function asset(string $path): string
{
    return base_url() . '/assets/' . ltrim($path, '/');
}

/** Include a reusable UI piece from views/components/ with parameters. */
function component(string $name, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    include BASE_PATH . '/views/components/' . $name . '.php';
    return (string) ob_get_clean();
}
