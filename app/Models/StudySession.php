<?php
/**
 * app/Models/StudySession.php  -  meetings: online, physical or hybrid, for a faculty or for one study group.
 * Group meetings are only visible to that group's members.
 */
class StudySession
{
    private const SELECT = "SELECT ss.*, s.code AS subject, s.name AS subject_name, s.faculty AS subject_faculty, u.name AS host,
            g.owner_id AS group_owner_id, g.name AS group_name,
            (SELECT COUNT(*) FROM session_attendees a WHERE a.session_id = ss.id AND a.status = 'going') AS attendees
         FROM study_sessions ss
         JOIN subjects s ON s.id = ss.subject_id
         JOIN users u ON u.id = ss.host_id
         LEFT JOIN study_groups g ON g.id = ss.group_id";

    public static function find(int $id): ?array
    {
        return Database::one(self::SELECT . ' WHERE ss.id = ?', [$id]);
    }

    /** WHERE pieces shared by every list: the viewer's faculty, and group meetings only for members. */
    private static function visibility(int $userId, ?string $faculty): array
    {
        if ($faculty === null) {
            return ['1=1', []];
        }
        return [
            's.faculty = ? AND (ss.group_id IS NULL OR EXISTS (SELECT 1 FROM group_members m WHERE m.group_id = ss.group_id AND m.user_id = ?))',
            [$faculty, $userId],
        ];
    }

    public static function upcoming(int $userId, int $limit = 3, ?string $faculty = null): array
    {
        [$where, $params] = self::visibility($userId, $faculty);
        return self::attachPreview(
            Database::all(self::SELECT . " WHERE ss.starts_at >= NOW() AND {$where} ORDER BY ss.starts_at LIMIT " . (int) $limit, $params),
            $userId
        );
    }

    public static function search(array $f, int $userId, int $page, int $perPage = 9): array
    {
        $past = ($f['when'] ?? '') === 'past';
        [$visible, $params] = self::visibility($userId, $f['faculty'] ?? null);
        $where = [$past ? 'ss.starts_at < NOW()' : 'ss.starts_at >= NOW()', $visible];
        if (($f['q'] ?? '') !== '') {
            $where[] = '(ss.title LIKE ? OR ss.description LIKE ? OR ss.location LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like, $like);
        }
        if (($f['mode'] ?? '') !== '') {
            $where[] = 'ss.mode = ?';
            $params[] = $f['mode'];
        }
        if (!empty($f['mine'])) {
            $where[] = "EXISTS (SELECT 1 FROM session_attendees a WHERE a.session_id = ss.id AND a.user_id = ? AND a.status = 'going')";
            $params[] = $userId;
        }
        $sql = implode(' AND ', $where);
        $total = (int) Database::value("SELECT COUNT(*) FROM study_sessions ss JOIN subjects s ON s.id = ss.subject_id WHERE {$sql}", $params);
        $offset = ($page - 1) * $perPage;
        $order = $past ? 'ss.starts_at DESC' : 'ss.starts_at ASC';
        $rows = Database::all(self::SELECT . " WHERE {$sql} ORDER BY {$order} LIMIT {$perPage} OFFSET {$offset}", $params);
        return ['rows' => self::attachPreview($rows, $userId), 'total' => $total, 'pages' => (int) ceil($total / $perPage)];
    }

    public static function forGroup(int $groupId, int $userId, bool $past = false): array
    {
        $when = $past ? 'ss.starts_at < NOW() ORDER BY ss.starts_at DESC LIMIT 6' : 'ss.starts_at >= NOW() ORDER BY ss.starts_at';
        return self::attachPreview(Database::all(self::SELECT . " WHERE ss.group_id = ? AND {$when}", [$groupId]), $userId);
    }

    /** Adds my_status (going, cant or null) and up to two names for the avatar stack. */
    private static function attachPreview(array $sessions, int $userId): array
    {
        foreach ($sessions as &$s) {
            $s['my_status'] = self::statusFor((int) $s['id'], $userId);
            $s['preview'] = array_column(Database::all(
                "SELECT u.name FROM session_attendees a JOIN users u ON u.id = a.user_id
                 WHERE a.session_id = ? AND a.status = 'going' ORDER BY a.joined_at LIMIT 2",
                [$s['id']]
            ), 'name');
        }
        return $sessions;
    }

    public static function statusFor(int $id, int $userId): ?string
    {
        $status = Database::value('SELECT status FROM session_attendees WHERE session_id = ? AND user_id = ?', [$id, $userId]);
        return $status === null ? null : (string) $status;
    }

    /** Faculty rule first, then group meetings are for members only. Hosts and admins always pass. */
    public static function canSee(array $session, array $user): bool
    {
        if ($user['role'] === 'admin' || (int) $session['host_id'] === (int) $user['id']) {
            return true;
        }
        if ($user['faculty'] !== $session['subject_faculty']) {
            return false;
        }
        return empty($session['group_id']) || Group::isMember((int) $session['group_id'], (int) $user['id']);
    }

    /** The person who scheduled it, the group's owner, or an admin. */
    public static function canManage(array $session, array $user): bool
    {
        return $user['role'] === 'admin'
            || (int) $session['host_id'] === (int) $user['id']
            || (int) ($session['group_owner_id'] ?? 0) === (int) $user['id'];
    }

    public static function create(int $hostId, array $d): int
    {
        return Database::transaction(function () use ($hostId, $d) {
            $id = Database::insert(
                'INSERT INTO study_sessions (host_id, subject_id, group_id, title, description, mode, starts_at, ends_at, location, meeting_url, capacity)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$hostId, $d['subject_id'], $d['group_id'], $d['title'], $d['description'], $d['mode'], $d['starts_at'], $d['ends_at'], $d['location'], $d['meeting_url'], $d['capacity']]
            );
            Database::run('INSERT INTO session_attendees (session_id, user_id) VALUES (?, ?)', [$id, $hostId]);
            return $id;
        });
    }

    public static function update(int $id, array $d): void
    {
        Database::run(
            'UPDATE study_sessions SET subject_id = ?, title = ?, description = ?, mode = ?, starts_at = ?, ends_at = ?, location = ?, meeting_url = ?, capacity = ? WHERE id = ?',
            [$d['subject_id'], $d['title'], $d['description'], $d['mode'], $d['starts_at'], $d['ends_at'], $d['location'], $d['meeting_url'], $d['capacity'], $id]
        );
    }

    /** Going. Locks the row so two students cannot take the last seat. Returns false when full. */
    public static function join(int $id, int $userId): bool
    {
        return Database::transaction(function () use ($id, $userId) {
            $capacity = (int) Database::value('SELECT capacity FROM study_sessions WHERE id = ? FOR UPDATE', [$id]);
            if (self::statusFor($id, $userId) === 'going') {
                return true;
            }
            $count = (int) Database::value("SELECT COUNT(*) FROM session_attendees WHERE session_id = ? AND status = 'going'", [$id]);
            if ($count >= $capacity) {
                return false;
            }
            Database::run(
                "INSERT INTO session_attendees (session_id, user_id, status) VALUES (?, ?, 'going')
                 ON DUPLICATE KEY UPDATE status = 'going'",
                [$id, $userId]
            );
            return true;
        });
    }

    /** Can't go. Frees the seat and remembers the answer. */
    public static function decline(int $id, int $userId): void
    {
        Database::run(
            "INSERT INTO session_attendees (session_id, user_id, status) VALUES (?, ?, 'cant')
             ON DUPLICATE KEY UPDATE status = 'cant'",
            [$id, $userId]
        );
    }

    public static function goingCount(int $id): int
    {
        return (int) Database::value("SELECT COUNT(*) FROM session_attendees WHERE session_id = ? AND status = 'going'", [$id]);
    }

    public static function removeFromGroupMeetings(int $groupId, int $userId): void
    {
        Database::run(
            'DELETE a FROM session_attendees a JOIN study_sessions ss ON ss.id = a.session_id
             WHERE ss.group_id = ? AND a.user_id = ? AND ss.starts_at >= NOW() AND ss.host_id <> a.user_id',
            [$groupId, $userId]
        );
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM study_sessions WHERE id = ?', [$id]);
    }

    /** Who to tell about a change: every member for a group meeting, otherwise everyone going. */
    public static function peopleToNotify(array $session): array
    {
        if (!empty($session['group_id'])) {
            return array_map('intval', array_column(Group::members((int) $session['group_id']), 'id'));
        }
        return array_map('intval', array_column(Database::all(
            "SELECT user_id FROM session_attendees WHERE session_id = ? AND status = 'going'",
            [$session['id']]
        ), 'user_id'));
    }
}
