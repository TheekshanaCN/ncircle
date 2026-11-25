<?php
/** app/Models/Attachment.php  -  images, files and GIFs on questions, answers and group posts */
class Attachment
{
    public static function create(string $ownerType, int $ownerId, int $userId, string $kind, string $name, ?string $stored, string $mime, int $size, ?int $width, ?int $height, ?string $giphyId): void
    {
        Database::run(
            'INSERT INTO attachments (owner_type, owner_id, user_id, kind, original_name, stored_name, mime, size, width, height, giphy_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$ownerType, $ownerId, $userId, $kind, $name, $stored, $mime, $size, $width, $height, $giphyId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM attachments WHERE id = ?', [$id]);
    }

    /** [ownerId => [attachment, ...]] */
    public static function forOwners(string $ownerType, array $ownerIds): array
    {
        if (!$ownerIds) {
            return [];
        }
        $marks = implode(',', array_fill(0, count($ownerIds), '?'));
        $rows = Database::all(
            "SELECT * FROM attachments WHERE owner_type = ? AND owner_id IN ({$marks}) ORDER BY kind, id",
            array_merge([$ownerType], array_values($ownerIds))
        );
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[(int) $row['owner_id']][] = $row;
        }
        return $grouped;
    }

    /** Adds an 'attachments' list to each row. */
    public static function attachTo(array $rows, string $ownerType): array
    {
        $grouped = self::forOwners($ownerType, array_map('intval', array_column($rows, 'id')));
        foreach ($rows as &$row) {
            $row['attachments'] = $grouped[(int) $row['id']] ?? [];
        }
        return $rows;
    }

    /** Removes the rows and their files from disk. */
    public static function deleteFor(string $ownerType, array $ownerIds): void
    {
        foreach (self::forOwners($ownerType, $ownerIds) as $list) {
            foreach ($list as $row) {
                if ($row['stored_name']) {
                    $path = self::path($row['stored_name']);
                    if (is_file($path)) {
                        unlink($path);
                    }
                }
            }
        }
        if ($ownerIds) {
            $marks = implode(',', array_fill(0, count($ownerIds), '?'));
            Database::run("DELETE FROM attachments WHERE owner_type = ? AND owner_id IN ({$marks})", array_merge([$ownerType], array_values($ownerIds)));
        }
    }

    public static function path(string $stored): string
    {
        return config('upload.attachments.dir') . '/' . basename($stored);
    }

    /** Same rules as the post it belongs to: faculty, hidden content and group membership. */
    public static function canView(array $a, array $user): bool
    {
        if ($user['role'] === 'admin') {
            return true;
        }
        $id = (int) $a['owner_id'];
        $mine = (int) $a['user_id'] === (int) $user['id'];
        switch ($a['owner_type']) {
            case 'question':
                $q = Question::find($id);
                return $q !== null && ($mine || (!$q['is_hidden'] && $user['faculty'] === $q['subject_faculty']));
            case 'answer':
                $answer = Answer::find($id);
                $q = $answer !== null ? Question::find((int) $answer['question_id']) : null;
                return $q !== null && ($mine || (!$answer['is_hidden'] && !$q['is_hidden'] && $user['faculty'] === $q['subject_faculty']));
            case 'group_post':
                $post = Group::findPost($id);
                return $post !== null && !$post['is_hidden'] && Group::isMember((int) $post['group_id'], (int) $user['id']);
        }
        return false;
    }
}
