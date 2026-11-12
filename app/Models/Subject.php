<?php
/** app/Models/Subject.php  -  NSBM modules */
class Subject
{
    /** Active subjects, limited to one faculty when $faculty is given. */
    public static function active(?string $faculty = null): array
    {
        if ($faculty !== null) {
            return Database::all('SELECT * FROM subjects WHERE is_active = 1 AND faculty = ? ORDER BY code', [$faculty]);
        }
        return Database::all('SELECT * FROM subjects WHERE is_active = 1 ORDER BY code');
    }

    /** [id => 'CS204 Database Systems'] for form selects */
    public static function options(?string $faculty = null): array
    {
        $options = [];
        foreach (self::active($faculty) as $s) {
            $options[$s['id']] = $s['code'] . ' ' . $s['name'];
        }
        return $options;
    }

    public static function isActive(int $id, ?string $faculty = null): bool
    {
        if ($faculty !== null) {
            return Database::value('SELECT 1 FROM subjects WHERE id = ? AND is_active = 1 AND faculty = ?', [$id, $faculty]) !== null;
        }
        return Database::value('SELECT 1 FROM subjects WHERE id = ? AND is_active = 1', [$id]) !== null;
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM subjects WHERE id = ?', [$id]);
    }

    public static function withCounts(): array
    {
        return Database::all(
            "SELECT s.*, (SELECT COUNT(*) FROM resources r WHERE r.subject_id = s.id AND r.status = 'approved') AS notes
             FROM subjects s ORDER BY s.is_active DESC, s.code"
        );
    }

    public static function codeExists(string $code, int $exceptId = 0): bool
    {
        return Database::value('SELECT 1 FROM subjects WHERE code = ? AND id <> ?', [strtoupper($code), $exceptId]) !== null;
    }

    public static function create(array $d): void
    {
        Database::run(
            'INSERT INTO subjects (code, name, faculty, year) VALUES (?, ?, ?, ?)',
            [strtoupper($d['code']), $d['name'], $d['faculty'], (int) $d['year']]
        );
    }

    public static function update(int $id, array $d): void
    {
        Database::run(
            'UPDATE subjects SET code = ?, name = ?, faculty = ?, year = ? WHERE id = ?',
            [strtoupper($d['code']), $d['name'], $d['faculty'], (int) $d['year'], $id]
        );
    }

    public static function toggle(int $id): void
    {
        Database::run('UPDATE subjects SET is_active = 1 - is_active WHERE id = ?', [$id]);
    }
}
