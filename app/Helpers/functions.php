<?php
/**
 * app/Helpers/functions.php  -  global helpers
 */

/** Escape output. Use on EVERY value printed in a view. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Read .env once into $_ENV. Real environment variables win over the file. */
function load_env(string $file): void
{
    if (!is_file($file)) {
        return;
    }
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        if (getenv($key) === false && !isset($_ENV[$key])) {
            $_ENV[$key] = trim(trim($value), "\"'");
        }
    }
}

function env_value(string $key, $default = null)
{
    $value = $_ENV[$key] ?? getenv($key);
    return $value === false || $value === null ? $default : $value;
}

/** config('upload.max_size') reads config/upload.php -> ['max_size'] */
function config(string $path, $default = null)
{
    static $loaded = [];
    $parts = explode('.', $path);
    $file = array_shift($parts);
    if (!isset($loaded[$file])) {
        $full = BASE_PATH . '/config/' . $file . '.php';
        $loaded[$file] = is_file($full) ? require $full : [];
    }
    $value = $loaded[$file];
    foreach ($parts as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
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

/**
 * Absolute link for emails. Production uses APP_URL only, because the Host
 * header is user-controlled. Local debug follows the current request.
 */
function absolute_url(string $path): string
{
    if (APP_DEBUG && !empty($_SERVER['HTTP_HOST'])) {
        $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        return $scheme . '://' . $_SERVER['HTTP_HOST'] . url($path);
    }
    return rtrim((string) env_value('APP_URL', ''), '/') . '/' . ltrim($path, '/');
}

/** asset('css/theme.css') -> /<base>/assets/css/theme.css */
function asset(string $path): string
{
    return base_url() . '/assets/' . ltrim($path, '/');
}

/** Address of a person's photo, or null when they have none. ?v= changes with the photo, so browsers can cache it. */
function avatar_url($userId, ?string $stored): ?string
{
    return $stored === null ? null : url('/profile/' . (int) $userId . '/photo?v=' . substr($stored, 0, 8));
}

/** One-time value that lets this page's own inline script run under the Content-Security-Policy. */
function csp_nonce(): string
{
    static $nonce = null;
    return $nonce ??= base64_encode(random_bytes(16));
}

/** Current path without the base folder, e.g. /resources/12 */
function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = base_url();
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }
    return '/' . trim($path, '/');
}

/** True when the current page is $prefix or below it. */
function is_path(string $prefix): bool
{
    $path = current_path();
    return $path === $prefix || ($prefix !== '/' && str_starts_with($path, rtrim($prefix, '/') . '/'));
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

function csrf_token(): string
{
    return Csrf::token();
}

/** Hidden input for every POST form. */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

/** Previous input after a failed validation. */
function old(string $key, $default = '')
{
    return Session::peek('_old', [])[$key] ?? $default;
}

/** Validation message for one field ('' when there is none). */
function field_error(string $key): string
{
    return (string) (Session::peek('_errors', [])[$key] ?? '');
}

/** The signed-in user row, or null. */
function auth_user(): ?array
{
    return Auth::user();
}

/** '2h ago' style label. */
function time_ago(?string $datetime): string
{
    if (!$datetime) {
        return '';
    }
    $seconds = max(0, time() - strtotime($datetime));
    if ($seconds < 60) {
        return 'just now';
    }
    if ($seconds < 3600) {
        return floor($seconds / 60) . 'm ago';
    }
    if ($seconds < 86400) {
        return floor($seconds / 3600) . 'h ago';
    }
    if ($seconds < 172800) {
        return 'Yesterday';
    }
    if ($seconds < 604800) {
        return floor($seconds / 86400) . 'd ago';
    }
    return date('M j, Y', strtotime($datetime));
}

/** 'Nimesh Perera' -> 'NP' */
function initials(?string $name): string
{
    $words = preg_split('/\s+/', trim((string) $name)) ?: [];
    $out = '';
    foreach (array_slice($words, 0, 2) as $word) {
        $out .= mb_strtoupper(mb_substr($word, 0, 1));
    }
    return $out !== '' ? $out : '?';
}

/** 2516582 -> '2.4 MB' */
function format_bytes(int $bytes): string
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    return max(1, (int) round($bytes / 1024)) . ' KB';
}

/** 1800 -> '1.8K' */
function compact_number(int $value): string
{
    if ($value >= 1000) {
        return rtrim(rtrim(number_format($value / 1000, 1), '0'), '.') . 'K';
    }
    return (string) $value;
}

/** 408 -> '6h 48m' */
function format_minutes(int $minutes): string
{
    $hours = intdiv($minutes, 60);
    $rest = $minutes % 60;
    return $hours > 0 ? $hours . 'h' . ($rest ? ' ' . $rest . 'm' : '') : $rest . 'm';
}

/** Current GET URL with some query values changed. null removes a key. */
function query_url(array $changes): string
{
    $query = array_merge($_GET, $changes);
    $query = array_filter($query, fn ($v) => $v !== null && $v !== '' && $v !== []);
    unset($query['partial']);
    $qs = http_build_query($query);
    return url(current_path()) . ($qs !== '' ? '?' . $qs : '');
}
