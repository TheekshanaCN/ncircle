<?php
/** app/Models/Question.php  -  forum threads */
class Question
{
    private const SELECT = 'SELECT q.*, u.name AS author, u.username AS author_username, s.code AS subject, s.name AS subject_name, s.faculty AS subject_faculty,
            (SELECT COUNT(*) FROM answers a WHERE a.question_id = q.id AND a.is_hidden = 0) AS replies,
            (SELECT au.name FROM answers a JOIN users au ON au.id = a.user_id
              WHERE a.question_id = q.id AND a.is_hidden = 0 ORDER BY a.is_accepted DESC, a.created_at ASC LIMIT 1) AS lead_answerer,
            (SELECT COUNT(*) FROM answer_votes v JOIN answers a ON a.id = v.answer_id WHERE a.question_id = q.id) AS upvotes
         FROM questions q JOIN users u ON u.id = q.user_id JOIN subjects s ON s.id = q.subject_id';

    public static function find(int $id): ?array
    {
        return Database::one(self::SELECT . ' WHERE q.id = ?', [$id]);
    }

    public static function recent(int $limit = 3, ?string $faculty = null): array
    {
        $where = $faculty !== null ? ' AND s.faculty = ?' : '';
        return Database::all(
            self::SELECT . " WHERE q.is_hidden = 0{$where} ORDER BY q.created_at DESC LIMIT " . (int) $limit,
            $faculty !== null ? [$faculty] : []
        );
    }

    public static function search(array $f, int $page, int $perPage = 10): array
    {
        $where = ['q.is_hidden = 0'];
        $params = [];
        if (($f['q'] ?? '') !== '') {
            $where[] = '(q.title LIKE ? OR q.body LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like);
        }
        if (($f['faculty'] ?? null) !== null) {
            $where[] = 's.faculty = ?';
            $params[] = $f['faculty'];
        }
        if (!empty($f['subject'])) {
            $where[] = 'q.subject_id = ?';
            $params[] = (int) $f['subject'];
        }
        $status = $f['status'] ?? '';
        if ($status === 'solved') {
            $where[] = 'q.solved_at IS NOT NULL';
        } elseif ($status === 'active') {
            $where[] = 'q.solved_at IS NULL AND EXISTS (SELECT 1 FROM answers a WHERE a.question_id = q.id AND a.is_hidden = 0)';
        } elseif ($status === 'unanswered') {
            $where[] = 'q.solved_at IS NULL AND NOT EXISTS (SELECT 1 FROM answers a WHERE a.question_id = q.id AND a.is_hidden = 0)';
        }
        $sql = implode(' AND ', $where);
        $total = (int) Database::value("SELECT COUNT(*) FROM questions q JOIN subjects s ON s.id = q.subject_id WHERE {$sql}", $params);
        $offset = ($page - 1) * $perPage;
        $rows = Database::all(self::SELECT . " WHERE {$sql} ORDER BY q.created_at DESC LIMIT {$perPage} OFFSET {$offset}", $params);
        return ['rows' => $rows, 'total' => $total, 'pages' => (int) ceil($total / $perPage)];
    }

    public static function create(int $userId, int $subjectId, string $title, string $body): int
    {
        return Database::insert('INSERT INTO questions (user_id, subject_id, title, body) VALUES (?, ?, ?, ?)', [$userId, $subjectId, $title, $body]);
    }

    public static function addView(int $id): void
    {
        Database::run('UPDATE questions SET views = views + 1 WHERE id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        $answerIds = array_map('intval', array_column(Database::all('SELECT id FROM answers WHERE question_id = ?', [$id]), 'id'));
        Attachment::deleteFor('answer', $answerIds);
        Attachment::deleteFor('question', [$id]);
        Database::run('DELETE FROM questions WHERE id = ?', [$id]);
    }

    public static function setHidden(int $id, bool $hidden): void
    {
        Database::run('UPDATE questions SET is_hidden = ? WHERE id = ?', [$hidden ? 1 : 0, $id]);
    }

    /** ['label' => 'SOLVED IN 11 MINS', 'tone' => 'lime|dark|muted'] for the thread pill */
    public static function statusBadge(array $q): array
    {
        if (!empty($q['solved_at'])) {
            $minutes = max(1, (int) round((strtotime($q['solved_at']) - strtotime($q['created_at'])) / 60));
            $span = $minutes < 60 ? $minutes . ' MINS' : ($minutes < 1440 ? round($minutes / 60) . ' HRS' : round($minutes / 1440) . ' DAYS');
            return ['label' => 'SOLVED IN ' . $span, 'tone' => 'lime'];
        }
        return (int) $q['replies'] > 0
            ? ['label' => 'ACTIVE DISCUSSION', 'tone' => 'dark']
            : ['label' => 'NEEDS ANSWER', 'tone' => 'muted'];
    }
}
