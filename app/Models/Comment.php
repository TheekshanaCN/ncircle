<?php
/** app/Models/Comment.php  -  comments on a note */
class Comment
{
    public static function forResource(int $resourceId): array
    {
        return Database::all(
            'SELECT c.*, u.name AS author, u.username, u.avatar FROM comments c JOIN users u ON u.id = c.user_id
             WHERE c.resource_id = ? AND c.is_hidden = 0 ORDER BY c.created_at ASC',
            [$resourceId]
        );
    }

    public static function create(int $resourceId, int $userId, string $body): int
    {
        return Database::insert('INSERT INTO comments (resource_id, user_id, body) VALUES (?, ?, ?)', [$resourceId, $userId, $body]);
    }
}
