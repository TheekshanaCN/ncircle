<?php
/**
 * app/Core/Session.php  -  secure session start plus one-request "flash" data.
 */
class Session
{
    private static array $flashNow = [];

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        session_name('ncircle_session');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);
        ini_set('session.use_strict_mode', '1');
        session_start();

        // Values flashed on the previous request are readable for this one only.
        self::$flashNow = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /** Read a value flashed by the previous request. */
    public static function peek(string $key, $default = null)
    {
        return self::$flashNow[$key] ?? $default;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 3600,
                'path'     => $p['path'],
                'httponly' => $p['httponly'],
                'samesite' => $p['samesite'],
                'secure'   => $p['secure'],
            ]);
        }
        session_destroy();
    }
}
