<?php
/**
 * app/Core/Response.php  -  redirects, JSON and error pages.
 */
class Response
{
    public static function redirect(string $path): void
    {
        header('Location: ' . url($path), true, 302);
        exit;
    }

    /** Go back to the form page. Only same-site paths are accepted. */
    public static function back(string $fallback = '/'): void
    {
        $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
        $parts = parse_url($referer);
        $sameHost = isset($parts['host'], $_SERVER['HTTP_HOST'])
            && strtolower($parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '')) === strtolower($_SERVER['HTTP_HOST']);
        if ($sameHost) {
            $target = ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
            header('Location: ' . $target, true, 302);
            exit;
        }
        self::redirect($fallback);
    }

    public static function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    /** Render views/errors/<code>.php (or JSON for AJAX) and stop. */
    public static function error(int $code, ?Throwable $debug = null): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code($code);
        $messages = [403 => 'You do not have access to this.', 404 => 'Page not found.', 419 => 'Your session expired. Please try again.', 500 => 'Something went wrong.'];
        if (Request::wantsJson()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $messages[$code] ?? 'Error', 'data' => (object) []]);
            exit;
        }
        $view = in_array($code, [403, 404], true) ? $code : 500;
        $title = "Error {$code} | Ncircle";
        $errorMessage = $messages[$code] ?? 'Something went wrong.';
        ob_start();
        require BASE_PATH . "/views/errors/{$view}.php";
        $content = ob_get_clean();
        require BASE_PATH . '/views/layouts/main.php';
        exit;
    }
}
