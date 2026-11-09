<?php
/** app/Models/Download.php  -  logs a download and bumps the note's counter together */
class Download
{
    public static function record(int $resourceId, int $userId): void
    {
        Database::transaction(function () use ($resourceId, $userId) {
            Database::run('INSERT INTO downloads (resource_id, user_id) VALUES (?, ?)', [$resourceId, $userId]);
            Database::run('UPDATE resources SET downloads_count = downloads_count + 1 WHERE id = ?', [$resourceId]);
        });
    }
}
