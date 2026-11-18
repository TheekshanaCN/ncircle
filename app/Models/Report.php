<?php
/** app/Models/Report.php  -  content students flag for the moderation queue */
class Report
{
    public const TYPES = ['resource', 'question', 'answer', 'comment', 'help_request', 'group_post'];

    /** One report per student per item. */
    public static function create(int $reporterId, string $type, int $targetId, string $reason): void
    {
        Database::run(
            'INSERT IGNORE INTO reports (reporter_id, target_type, target_id, reason) VALUES (?, ?, ?, ?)',
            [$reporterId, $type, $targetId, $reason]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM reports WHERE id = ?', [$id]);
    }

    public static function open(): array
    {
        $rows = Database::all(
            "SELECT rp.*, u.name AS reporter FROM reports rp JOIN users u ON u.id = rp.reporter_id
             WHERE rp.status = 'open' ORDER BY rp.created_at ASC"
        );
        foreach ($rows as &$row) {
            $row['target'] = self::target($row['target_type'], (int) $row['target_id']);
        }
        return $rows;
    }

    /** Text and link for the reported item, or null when it has been deleted. */
    public static function target(string $type, int $id): ?array
    {
        switch ($type) {
            case 'resource':
                $r = Database::one('SELECT r.id, r.title, s.faculty FROM resources r JOIN subjects s ON s.id = r.subject_id WHERE r.id = ?', [$id]);
                return $r ? ['text' => $r['title'], 'link' => '/resources/' . $r['id'], 'faculty' => $r['faculty']] : null;
            case 'question':
                $r = Database::one('SELECT q.id, q.title, s.faculty FROM questions q JOIN subjects s ON s.id = q.subject_id WHERE q.id = ?', [$id]);
                return $r ? ['text' => $r['title'], 'link' => '/forum/' . $r['id'], 'faculty' => $r['faculty']] : null;
            case 'answer':
                $r = Database::one('SELECT a.question_id, a.body, s.faculty FROM answers a JOIN questions q ON q.id = a.question_id JOIN subjects s ON s.id = q.subject_id WHERE a.id = ?', [$id]);
                return $r ? ['text' => $r['body'], 'link' => '/forum/' . $r['question_id'], 'faculty' => $r['faculty']] : null;
            case 'comment':
                $r = Database::one('SELECT c.resource_id, c.body, s.faculty FROM comments c JOIN resources r ON r.id = c.resource_id JOIN subjects s ON s.id = r.subject_id WHERE c.id = ?', [$id]);
                return $r ? ['text' => $r['body'], 'link' => '/resources/' . $r['resource_id'], 'faculty' => $r['faculty']] : null;
            case 'group_post':
                $r = Database::one('SELECT p.group_id, p.body, s.faculty FROM group_posts p JOIN study_groups g ON g.id = p.group_id JOIN subjects s ON s.id = g.subject_id WHERE p.id = ?', [$id]);
                return $r ? ['text' => $r['body'] !== '' ? $r['body'] : 'Post with an image, file or GIF', 'link' => '/groups/' . $r['group_id'], 'faculty' => $r['faculty']] : null;
            case 'help_request':
                $r = Database::one('SELECT id, title FROM help_requests WHERE id = ?', [$id]);
                return $r ? ['text' => $r['title'], 'link' => '/members/requests/' . $r['id'], 'faculty' => null] : null;
        }
        return null;
    }

    public static function close(int $id, string $status, int $adminId): void
    {
        Database::run('UPDATE reports SET status = ?, resolved_by = ?, resolved_at = NOW() WHERE id = ?', [$status, $adminId, $id]);
    }

    /** Takes the reported item off the site. */
    public static function hideTarget(array $report): void
    {
        $id = (int) $report['target_id'];
        match ($report['target_type']) {
            'resource' => Database::run("UPDATE resources SET status = 'rejected', reject_reason = 'Removed after a report' WHERE id = ?", [$id]),
            'question' => Question::setHidden($id, true),
            'answer'   => Answer::setHidden($id, true),
            'comment'  => Database::run('UPDATE comments SET is_hidden = 1 WHERE id = ?', [$id]),
            'group_post' => Database::run('UPDATE group_posts SET is_hidden = 1 WHERE id = ?', [$id]),
            'help_request' => Database::run('UPDATE help_requests SET is_hidden = 1 WHERE id = ?', [$id]),
        };
    }
}
