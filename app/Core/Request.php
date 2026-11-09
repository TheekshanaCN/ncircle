<?php
/**
 * app/Core/Request.php  -  small wrapper around the request superglobals.
 */
class Request
{
    private static ?array $json = null;

    /** JSON body for AJAX calls (SC.request sends JSON). */
    private static function json(): array
    {
        if (self::$json === null) {
            $isJson = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
            $data = $isJson ? json_decode((string) file_get_contents('php://input'), true) : [];
            self::$json = is_array($data) ? $data : [];
        }
        return self::$json;
    }

    public static function input(string $key, $default = '')
    {
        $value = $_POST[$key] ?? self::json()[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    /** All POST values, trimmed. Use raw() for passwords. */
    public static function all(): array
    {
        return array_map(fn ($v) => is_string($v) ? trim($v) : $v, $_POST);
    }

    /** Raw value, for passwords: never trimmed. */
    public static function raw(string $key): string
    {
        $value = $_POST[$key] ?? '';
        return is_string($value) ? $value : '';
    }

    public static function query(string $key, $default = '')
    {
        $value = $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    public static function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    public static function ip(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    /** Upload bigger than PHP's post_max_size: PHP silently empties $_POST. */
    public static function postTooLarge(): bool
    {
        $length = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        return $length > 0 && empty($_POST) && empty($_FILES)
            && str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data');
    }
}
