<?php
/** app/Models/Notification.php  -  in-app notifications */
class Notification
{
    public static function forUser(int $userId, int $page, int $perPage = 15): array
    {
        $total = (int) Database::value('SELECT COUNT(*) FROM notifications WHERE user_id = ?', [$userId]);
        $offset = ($page - 1) * $perPage;
        $rows = Database::all(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC, id DESC LIMIT {$perPage} OFFSET {$offset}",
            [$userId]
        );
        return ['rows' => $rows, 'total' => $total, 'pages' => (int) ceil($total / $perPage)];
    }

    public static function unreadCount(int $userId): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND read_at IS NULL', [$userId]);
    }

    public static function create(int $userId, string $icon, string $title, ?string $body, ?string $link): void
    {
        Database::run(
            'INSERT INTO notifications (user_id, icon, title, body, link) VALUES (?, ?, ?, ?, ?)',
            [$userId, $icon, $title, $body, $link]
        );
    }

    /** Scoped to the owner so one student cannot touch another's rows. */
    public static function findForUser(int $id, int $userId): ?array
    {
        return Database::one('SELECT * FROM notifications WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public static function markRead(int $id, int $userId): void
    {
        Database::run('UPDATE notifications SET read_at = COALESCE(read_at, NOW()) WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public static function markAllRead(int $userId): void
    {
        Database::run('UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL', [$userId]);
    }
}
