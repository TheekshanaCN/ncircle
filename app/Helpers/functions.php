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

/**
 * Render a reusable UI piece from views/components/<name>.php and RETURN the HTML.
 *   <?= component('status-badge', ['status' => 'pending']) ?>
 * The array keys become variables inside the component file.
 * (Parameter names start with __ so a component can safely receive a 'name' or 'data' key.)
 */
function component(string $__component, array $__data = []): string
{
    extract($__data, EXTR_SKIP);
    ob_start();
    include BASE_PATH . '/views/components/' . $__component . '.php';
    return (string) ob_get_clean();
}

/**
 * Turn an array into escaped HTML attributes.
 *   html_attrs(['data-id' => 5, 'disabled' => true, 'hidden' => false]) -> ' data-id="5" disabled'
 * true = bare attribute, false/null = skipped.
 */
function html_attrs(array $attrs): string
{
    $out = '';
    foreach ($attrs as $key => $value) {
        if ($value === false || $value === null) {
            continue;
        }
        $out .= $value === true
            ? ' ' . e((string) $key)
            : ' ' . e((string) $key) . '="' . e((string) $value) . '"';
    }
    return $out;
}
