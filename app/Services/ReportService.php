<?php
/** app/Services/ReportService.php  -  admin dashboard numbers and the reports page */
class ReportService
{
    public static function overview(): array
    {
        return [
            'users'     => (int) Database::value("SELECT COUNT(*) FROM users WHERE role = 'student'"),
            'notes'     => (int) Database::value("SELECT COUNT(*) FROM resources WHERE status = 'approved'"),
            'pending'   => (int) Database::value("SELECT COUNT(*) FROM resources WHERE status = 'pending'"),
            'reports'   => (int) Database::value("SELECT COUNT(*) FROM reports WHERE status = 'open'"),
            'questions' => (int) Database::value('SELECT COUNT(*) FROM questions WHERE is_hidden = 0'),
            'groups'    => (int) Database::value('SELECT COUNT(*) FROM study_groups'),
            'downloads' => (int) Database::value('SELECT COALESCE(SUM(downloads_count), 0) FROM resources'),
            'sessions'  => (int) Database::value('SELECT COUNT(*) FROM study_sessions WHERE starts_at >= NOW()'),
        ];
    }

    /** Most downloaded notes */
    public static function topNotes(int $limit = 10): array
    {
        return Database::all(
            "SELECT r.id, r.title, s.code AS subject, u.name AS uploader, r.downloads_count,
                    COALESCE((SELECT ROUND(AVG(value), 1) FROM ratings rt WHERE rt.resource_id = r.id), 0) AS rating
             FROM resources r JOIN subjects s ON s.id = r.subject_id JOIN users u ON u.id = r.user_id
             WHERE r.status = 'approved' ORDER BY r.downloads_count DESC LIMIT " . (int) $limit
        );
    }

    public static function topContributors(int $limit = 10): array
    {
        return Database::all(
            "SELECT u.id, u.name, u.faculty,
                    (SELECT COUNT(*) FROM resources r WHERE r.user_id = u.id AND r.status = 'approved') AS notes,
                    (SELECT COUNT(*) FROM answers a WHERE a.user_id = u.id AND a.is_hidden = 0) AS answers
             FROM users u WHERE u.role = 'student' ORDER BY notes DESC, answers DESC, u.name LIMIT " . (int) $limit
        );
    }

    public static function bySubject(): array
    {
        return Database::all(
            "SELECT s.code, s.name, s.faculty, COUNT(r.id) AS notes, COALESCE(SUM(r.downloads_count), 0) AS downloads
             FROM subjects s LEFT JOIN resources r ON r.subject_id = s.id AND r.status = 'approved'
             GROUP BY s.id ORDER BY notes DESC, s.code"
        );
    }

    /** Sends a CSV download of the per-subject report and stops. */
    public static function exportSubjectsCsv(): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="ncircle-subject-report-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Code', 'Subject', 'Faculty', 'Approved notes', 'Downloads']);
        foreach (self::bySubject() as $row) {
            fputcsv($out, array_map(fn ($v) => self::csvSafe((string) $v), [$row['code'], $row['name'], $row['faculty'], $row['notes'], $row['downloads']]));
        }
        fclose($out);
        exit;
    }

    /** Stops a spreadsheet from running admin-entered text as a formula. */
    private static function csvSafe(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }
}
