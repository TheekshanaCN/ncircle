<?php
/**
 * app/Core/Auth.php  -  who is signed in.
 */
class Auth
{
    private static ?array $user = null;
    private static bool $loaded = false;

    private const REMEMBER_COOKIE = 'ncircle_remember';
    private const REMEMBER_DAYS = 30;

    /** Session check only (no database), safe on error pages. */
    public static function check(): bool
    {
        return (int) Session::get('user_id', 0) > 0;
    }

    public static function id(): int
    {
        return (int) Session::get('user_id', 0);
    }

    /** Current user row. A blocked or deleted account is signed out here. */
    public static function user(): ?array
    {
        if (!self::$loaded) {
            self::$loaded = true;
            if (self::check()) {
                $user = User::find(self::id());
                if ($user === null || $user['status'] !== 'active') {
                    self::logout();
                } else {
                    self::$user = $user;
                    User::touchSeen($user);
                }
            }
        }
        return self::$user;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? '') === 'admin';
    }

    /** The only faculty this viewer may see, or null for admins, who see every faculty. */
    public static function facultyScope(): ?string
    {
        $user = self::user();
        return $user === null || $user['role'] === 'admin' ? null : $user['faculty'];
    }

    /** May the signed-in viewer open content that belongs to $faculty? */
    public static function canSeeFaculty(?string $faculty): bool
    {
        $scope = self::facultyScope();
        return $scope === null || $scope === $faculty;
    }

    public static function login(array $user, bool $remember = false): void
    {
        Session::regenerate();
        Session::set('user_id', (int) $user['id']);
        self::$user = $user;
        self::$loaded = true;
        User::touchSeen($user);
        if ($remember) {
            $raw = User::createToken((int) $user['id'], 'remember', self::REMEMBER_DAYS * 1440);
            self::setRememberCookie($raw, time() + self::REMEMBER_DAYS * 86400);
        }
    }

    /** "Remember me": sign back in from the long-lived cookie when the session has ended. */
    public static function restoreRemembered(): void
    {
        $raw = $_COOKIE[self::REMEMBER_COOKIE] ?? '';
        if (self::check() || !is_string($raw) || $raw === '') {
            return;
        }
        $userId = User::tokenOwner($raw, 'remember');
        $user = $userId !== null ? User::find($userId) : null;
        if ($user !== null && $user['status'] === 'active' && $user['email_verified_at'] !== null) {
            self::login($user);
        } else {
            self::setRememberCookie('', time() - 3600);
        }
    }

    private static function setRememberCookie(string $value, int $expires): void
    {
        setcookie(self::REMEMBER_COOKIE, $value, [
            'expires'  => $expires,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);
    }

    public static function logout(): void
    {
        if (self::check()) {
            User::deleteTokens(self::id(), 'remember');
        }
        self::setRememberCookie('', time() - 3600);
        self::$user = null;
        Session::destroy();
        Session::start();
    }
}
