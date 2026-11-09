<?php
/**
 * app/Models/HelpRequest.php  -  Find Members request board, open to every faculty.
 * Emails are never selected here: see contactEmail() for the one place they are revealed.
 */
class HelpRequest
{
    private const SELECT = "SELECT hr.*, u.name AS author, u.faculty AS author_faculty,
            (SELECT COUNT(*) FROM help_request_responses r WHERE r.request_id = hr.id) AS responses_count
         FROM help_requests hr JOIN users u ON u.id = hr.user_id";

    public static function find(int $id): ?array
    {
        $row = Database::one(self::SELECT . ' WHERE hr.id = ?', [$id]);
        return $row === null ? null : self::attachFaculties([$row])[0];
    }

    public static function search(array $f, int $userId, int $page, int $perPage = 8): array
    {
        $where = ['hr.is_hidden = 0'];
        $params = [];
        if (!empty($f['mine'])) {
            $where[] = 'hr.user_id = ?';
            $params[] = $userId;
        } else {
            $where[] = "hr.status = 'open'";
        }
        if (($f['q'] ?? '') !== '') {
            $where[] = '(hr.title LIKE ? OR hr.description LIKE ? OR hr.skills LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like, $like);
        }
        if (($f['type'] ?? '') !== '') {
            $where[] = 'hr.type = ?';
            $params[] = $f['type'];
        }
        if (($f['faculty'] ?? '') !== '') {
            // A request with no faculties listed welcomes everyone.
            $where[] = '(EXISTS (SELECT 1 FROM help_request_faculties x WHERE x.request_id = hr.id AND x.faculty = ?)
                         OR NOT EXISTS (SELECT 1 FROM help_request_faculties x WHERE x.request_id = hr.id))';
            $params[] = $f['faculty'];
        }
        $sql = implode(' AND ', $where);
        $total = (int) Database::value("SELECT COUNT(*) FROM help_requests hr WHERE {$sql}", $params);
        $offset = ($page - 1) * $perPage;
        $rows = Database::all(self::SELECT . " WHERE {$sql} ORDER BY hr.created_at DESC LIMIT {$perPage} OFFSET {$offset}", $params);
        return ['rows' => self::attachFaculties($rows), 'total' => $total, 'pages' => (int) ceil($total / $perPage)];
    }

    private static function attachFaculties(array $rows): array
    {
        foreach ($rows as &$row) {
            $row['faculties'] = array_column(Database::all('SELECT faculty FROM help_request_faculties WHERE request_id = ? ORDER BY faculty', [$row['id']]), 'faculty');
        }
        return $rows;
    }

    public static function create(int $userId, array $d, array $faculties): int
    {
        return Database::transaction(function () use ($userId, $d, $faculties) {
            $id = Database::insert(
                'INSERT INTO help_requests (user_id, type, title, description, skills) VALUES (?, ?, ?, ?, ?)',
                [$userId, $d['type'], $d['title'], $d['description'], ($d['skills'] ?? '') !== '' ? $d['skills'] : null]
            );
            foreach ($faculties as $faculty) {
                Database::run('INSERT INTO help_request_faculties (request_id, faculty) VALUES (?, ?)', [$id, $faculty]);
            }
            return $id;
        });
    }

    public static function close(int $id): void
    {
        Database::run("UPDATE help_requests SET status = 'closed' WHERE id = ?", [$id]);
    }

    public static function responses(int $requestId): array
    {
        return Database::all(
            'SELECT r.*, u.name, u.faculty, u.academic_year, u.skills FROM help_request_responses r
             JOIN users u ON u.id = r.user_id WHERE r.request_id = ? ORDER BY r.created_at',
            [$requestId]
        );
    }

    public static function responseBy(int $requestId, int $userId): ?array
    {
        return Database::one('SELECT * FROM help_request_responses WHERE request_id = ? AND user_id = ?', [$requestId, $userId]);
    }

    public static function findResponse(int $id): ?array
    {
        return Database::one('SELECT * FROM help_request_responses WHERE id = ?', [$id]);
    }

    /** One response per student, per request. */
    public static function respond(int $requestId, int $userId, string $message): void
    {
        Database::run(
            'INSERT IGNORE INTO help_request_responses (request_id, user_id, message) VALUES (?, ?, ?)',
            [$requestId, $userId, $message]
        );
    }

    /** Only a pending response can be answered, and only once. Returns false if it was already decided. */
    public static function decide(int $responseId, string $status): bool
    {
        return Database::run(
            "UPDATE help_request_responses SET status = ? WHERE id = ? AND status = 'pending'",
            [$status, $responseId]
        )->rowCount() > 0;
    }

    /** The other person's email, only after an accept. */
    public static function contactEmail(int $userId): string
    {
        return (string) Database::value('SELECT email FROM users WHERE id = ?', [$userId]);
    }
}
