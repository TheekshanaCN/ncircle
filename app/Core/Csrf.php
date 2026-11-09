<?php
/**
 * app/Core/Csrf.php  -  token per session, checked on every POST (see CsrfMiddleware).
 */
class Csrf
{
    public static function token(): string
    {
        $token = Session::get('_csrf');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf', $token);
        }
        return $token;
    }

    public static function valid(?string $token): bool
    {
        $known = Session::get('_csrf');
        return is_string($token) && $token !== '' && is_string($known) && hash_equals($known, $token);
    }
}
