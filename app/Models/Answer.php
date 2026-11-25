<?php
/** app/Models/Answer.php  -  forum replies, votes and the accepted answer */
class Answer
{
    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM answers WHERE id = ?', [$id]);
    }

    public static function forQuestion(int $questionId, int $viewerId): array
    {
        return Database::all(
            'SELECT a.*, u.name AS author, u.username,
                    (SELECT COUNT(*) FROM answer_votes v WHERE v.answer_id = a.id) AS votes,
                    (SELECT COUNT(*) FROM answer_votes v WHERE v.answer_id = a.id AND v.user_id = ?) AS has_voted
             FROM answers a JOIN users u ON u.id = a.user_id
             WHERE a.question_id = ? AND a.is_hidden = 0
             ORDER BY a.is_accepted DESC, votes DESC, a.created_at ASC',
            [$viewerId, $questionId]
        );
    }

    public static function create(int $questionId, int $userId, string $body): int
    {
        return Database::insert('INSERT INTO answers (question_id, user_id, body) VALUES (?, ?, ?)', [$questionId, $userId, $body]);
    }

    public static function update(int $id, string $body): void
    {
        Database::run('UPDATE answers SET body = ? WHERE id = ?', [$body, $id]);
    }

    /** Only one accepted answer per question. */
    public static function accept(array $answer): void
    {
        Database::transaction(function () use ($answer) {
            Database::run('UPDATE answers SET is_accepted = 0 WHERE question_id = ?', [$answer['question_id']]);
            Database::run('UPDATE answers SET is_accepted = 1 WHERE id = ?', [$answer['id']]);
            Database::run('UPDATE questions SET solved_at = COALESCE(solved_at, NOW()) WHERE id = ?', [$answer['question_id']]);
        });
    }

    public static function toggleVote(int $answerId, int $userId): void
    {
        $deleted = Database::run('DELETE FROM answer_votes WHERE answer_id = ? AND user_id = ?', [$answerId, $userId])->rowCount();
        if ($deleted === 0) {
            Database::run('INSERT INTO answer_votes (answer_id, user_id) VALUES (?, ?)', [$answerId, $userId]);
        }
    }

    public static function delete(array $answer): void
    {
        Attachment::deleteFor('answer', [(int) $answer['id']]);
        Database::run('DELETE FROM answers WHERE id = ?', [$answer['id']]);
    }

    public static function setHidden(int $id, bool $hidden): void
    {
        Database::run('UPDATE answers SET is_hidden = ? WHERE id = ?', [$hidden ? 1 : 0, $id]);
    }
}
