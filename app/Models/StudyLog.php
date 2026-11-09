<?php
/** app/Models/StudyLog.php  -  minutes a student logs. Drives the weekly goal and streak. */
class StudyLog
{
    public static function add(int $userId, int $minutes, string $note, string $date): void
    {
        Database::run(
            'INSERT INTO study_logs (user_id, minutes, note, logged_on) VALUES (?, ?, ?, ?)',
            [$userId, $minutes, $note !== '' ? $note : null, $date]
        );
    }

    public static function recent(int $userId, int $limit = 8): array
    {
        return Database::all('SELECT * FROM study_logs WHERE user_id = ? ORDER BY logged_on DESC, id DESC LIMIT ' . (int) $limit, [$userId]);
    }

    /** Minutes between two dates (inclusive). */
    public static function minutes(int $userId, string $from, string $to): int
    {
        return (int) Database::value(
            'SELECT COALESCE(SUM(minutes), 0) FROM study_logs WHERE user_id = ? AND logged_on BETWEEN ? AND ?',
            [$userId, $from, $to]
        );
    }

    /** Consecutive days with a log, counting back from today (or yesterday if today has none yet). */
    public static function streak(int $userId): int
    {
        $days = array_column(Database::all(
            'SELECT DISTINCT logged_on FROM study_logs WHERE user_id = ? AND logged_on <= CURDATE() ORDER BY logged_on DESC LIMIT 366',
            [$userId]
        ), 'logged_on');
        $cursor = new DateTimeImmutable('today');
        if (($days[0] ?? '') !== $cursor->format('Y-m-d')) {
            $cursor = $cursor->modify('-1 day');
        }
        $streak = 0;
        foreach ($days as $day) {
            if ($day !== $cursor->format('Y-m-d')) {
                break;
            }
            $streak++;
            $cursor = $cursor->modify('-1 day');
        }
        return $streak;
    }
}
