<?php
/** app/Models/User.php  -  accounts, one-time tokens and sign-in throttling */
class User
{
    public const MAX_ATTEMPTS = 5;
    public const ATTEMPT_WINDOW_MINUTES = 15;

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM users WHERE id = ?', [$id]);
    }

    /** Sign-in accepts the university email or the student ID. */
    public static function findByLogin(string $login): ?array
    {
        return Database::one('SELECT * FROM users WHERE email = ? OR student_id = ?', [strtolower($login), strtoupper($login)]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::one('SELECT * FROM users WHERE email = ?', [strtolower($email)]);
    }

    public static function studentIdExists(string $studentId): bool
    {
        return Database::value('SELECT 1 FROM users WHERE student_id = ?', [strtoupper($studentId)]) !== null;
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO users (name, username, student_id, email, faculty, academic_year, password_hash)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $data['name'],
                self::uniqueUsername($data['email']),
                strtoupper($data['student_id']),
                strtolower($data['email']),
                $data['faculty'],
                (int) $data['academic_year'],
                password_hash($data['password'], PASSWORD_DEFAULT),
            ]
        );
    }

    private static function uniqueUsername(string $email): string
    {
        $base = strtolower((string) preg_replace('/[^a-z0-9_]/i', '', strstr($email, '@', true) ?: 'student'));
        $base = $base !== '' ? substr($base, 0, 50) : 'student';
        $name = $base;
        for ($i = 2; Database::value('SELECT 1 FROM users WHERE username = ?', [$name]) !== null; $i++) {
            $name = $base . $i;
        }
        return $name;
    }

    /** Faculty is not here on purpose: only an admin can change it, see setFaculty(). */
    public static function updateProfile(int $id, array $data): void
    {
        Database::run(
            'UPDATE users SET name = ?, academic_year = ?, bio = ?, skills = ?, open_to_help = ?, listed_in_directory = ? WHERE id = ?',
            [
                $data['name'],
                (int) $data['academic_year'],
                $data['bio'] !== '' ? $data['bio'] : null,
                $data['skills'] !== '' ? $data['skills'] : null,
                $data['open_to_help'] ? 1 : 0,
                $data['listed_in_directory'] ? 1 : 0,
                $id,
            ]
        );
    }

    public static function setFaculty(int $id, string $faculty): void
    {
        Database::run('UPDATE users SET faculty = ? WHERE id = ?', [$faculty, $id]);
    }

    /** Find Members directory: active, verified students who chose to be listed. Never selects email. */
    public static function directory(array $f, int $page, int $perPage = 12): array
    {
        $where = ["role = 'student'", "status = 'active'", 'email_verified_at IS NOT NULL', 'listed_in_directory = 1'];
        $params = [];
        if (($f['q'] ?? '') !== '') {
            $where[] = '(name LIKE ? OR skills LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like);
        }
        if (($f['faculty'] ?? '') !== '') {
            $where[] = 'faculty = ?';
            $params[] = $f['faculty'];
        }
        if (!empty($f['open'])) {
            $where[] = 'open_to_help = 1';
        }
        $sql = implode(' AND ', $where);
        $total = (int) Database::value("SELECT COUNT(*) FROM users WHERE {$sql}", $params);
        $offset = ($page - 1) * $perPage;
        $rows = Database::all(
            "SELECT id, name, username, faculty, academic_year, bio, skills, open_to_help FROM users
             WHERE {$sql} ORDER BY open_to_help DESC, last_seen_at DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        return ['rows' => $rows, 'total' => $total, 'pages' => (int) ceil($total / $perPage)];
    }

    public static function setPassword(int $id, string $password): void
    {
        Database::run('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public static function setGoal(int $id, int $minutes): void
    {
        Database::run('UPDATE users SET weekly_goal_minutes = ? WHERE id = ?', [$minutes, $id]);
    }

    public static function markVerified(int $id): void
    {
        Database::run('UPDATE users SET email_verified_at = NOW() WHERE id = ? AND email_verified_at IS NULL', [$id]);
    }

    /** Written at most once a minute, so page views stay cheap. */
    public static function touchSeen(array $user): void
    {
        if (empty($user['last_seen_at']) || strtotime($user['last_seen_at']) < time() - 60) {
            Database::run('UPDATE users SET last_seen_at = NOW() WHERE id = ?', [$user['id']]);
        }
    }

    public static function setStatus(int $id, string $status): void
    {
        Database::run('UPDATE users SET status = ? WHERE id = ?', [$status, $id]);
    }

    public static function setRole(int $id, string $role): void
    {
        Database::run('UPDATE users SET role = ? WHERE id = ?', [$role, $id]);
    }

    public static function onlineCount(?string $faculty = null): int
    {
        if ($faculty !== null) {
            return (int) Database::value('SELECT COUNT(*) FROM users WHERE faculty = ? AND last_seen_at >= NOW() - INTERVAL 15 MINUTE', [$faculty]);
        }
        return (int) Database::value('SELECT COUNT(*) FROM users WHERE last_seen_at >= NOW() - INTERVAL 15 MINUTE');
    }

    public static function search(string $q, int $page, int $perPage = 15): array
    {
        $where = '1=1';
        $params = [];
        if ($q !== '') {
            $where = '(name LIKE ? OR email LIKE ? OR student_id LIKE ?)';
            $like = '%' . $q . '%';
            $params = [$like, $like, $like];
        }
        $total = (int) Database::value("SELECT COUNT(*) FROM users WHERE {$where}", $params);
        $offset = ($page - 1) * $perPage;
        $rows = Database::all(
            "SELECT u.*,
                    (SELECT COUNT(*) FROM resources r WHERE r.user_id = u.id) AS uploads
             FROM users u WHERE {$where} ORDER BY u.created_at DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        return ['rows' => $rows, 'total' => $total, 'pages' => (int) ceil($total / $perPage)];
    }

    /** Creates a single-use token and returns the raw value. Only its hash is stored. */
    public static function createToken(int $userId, string $type, int $minutes): string
    {
        Database::run('DELETE FROM user_tokens WHERE user_id = ? AND type = ?', [$userId, $type]);
        $raw = bin2hex(random_bytes(32));
        Database::run(
            'INSERT INTO user_tokens (user_id, type, token_hash, expires_at) VALUES (?, ?, ?, NOW() + INTERVAL ? MINUTE)',
            [$userId, $type, hash('sha256', $raw), $minutes]
        );
        return $raw;
    }

    /** User id for a live token, or null. Does not use it up. */
    public static function tokenOwner(string $raw, string $type): ?int
    {
        $id = Database::value(
            'SELECT user_id FROM user_tokens WHERE token_hash = ? AND type = ? AND expires_at > NOW()',
            [hash('sha256', $raw), $type]
        );
        return $id === null ? null : (int) $id;
    }

    public static function deleteTokens(int $userId, string $type): void
    {
        Database::run('DELETE FROM user_tokens WHERE user_id = ? AND type = ?', [$userId, $type]);
    }

    public static function tooManyAttempts(string $login): bool
    {
        $count = Database::value(
            'SELECT COUNT(*) FROM login_attempts WHERE identifier = ? AND ip = ? AND attempted_at >= NOW() - INTERVAL ? MINUTE',
            [strtolower($login), Request::ip(), self::ATTEMPT_WINDOW_MINUTES]
        );
        return (int) $count >= self::MAX_ATTEMPTS;
    }

    public static function recordFailedAttempt(string $login): void
    {
        Database::run('INSERT INTO login_attempts (identifier, ip) VALUES (?, ?)', [strtolower(substr($login, 0, 120)), Request::ip()]);
    }

    public static function clearAttempts(string $login): void
    {
        Database::run('DELETE FROM login_attempts WHERE identifier = ? AND ip = ?', [strtolower($login), Request::ip()]);
    }
}
