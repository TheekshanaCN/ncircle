<?php
/** app/Models/Rating.php  -  one rating per student per note. Rating again updates it. */
class Rating
{
    public static function forUser(int $resourceId, int $userId): int
    {
        return (int) Database::value('SELECT value FROM ratings WHERE resource_id = ? AND user_id = ?', [$resourceId, $userId]);
    }

    public static function save(int $resourceId, int $userId, int $value): void
    {
        Database::run(
            'INSERT INTO ratings (resource_id, user_id, value) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE value = VALUES(value)',
            [$resourceId, $userId, $value]
        );
    }

    public static function summary(int $resourceId): array
    {
        $row = Database::one('SELECT COALESCE(AVG(value), 0) AS rating, COUNT(*) AS rating_count FROM ratings WHERE resource_id = ?', [$resourceId]);
        return ['rating' => round((float) $row['rating'], 1), 'rating_count' => (int) $row['rating_count']];
    }
}
