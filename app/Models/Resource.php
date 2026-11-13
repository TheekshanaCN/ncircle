<?php
/** app/Models/Resource.php  -  study notes: listing, filters and review */
class Resource
{
    private const SELECT = "SELECT r.*, u.name AS uploader, u.username AS uploader_username, u.id AS uploader_id,
            s.code AS subject, s.name AS subject_name, s.year AS subject_year, s.faculty AS subject_faculty,
            c.name AS category, c.slug AS category_slug,
            COALESCE(rt.rating, 0) AS rating, COALESCE(rt.rating_count, 0) AS rating_count,
            (SELECT COUNT(*) FROM comments cm WHERE cm.resource_id = r.id AND cm.is_hidden = 0) AS comments_count";

    private const FROM = " FROM resources r
            JOIN users u ON u.id = r.user_id
            JOIN subjects s ON s.id = r.subject_id
            JOIN categories c ON c.id = r.category_id
            LEFT JOIN (SELECT resource_id, AVG(value) AS rating, COUNT(*) AS rating_count FROM ratings GROUP BY resource_id) rt
                   ON rt.resource_id = r.id";

    public const SORTS = [
        'relevance' => 'Relevance',
        'newest'    => 'Newest',
        'rating'    => 'Top rated',
        'downloads' => 'Most downloaded',
    ];

    public static function find(int $id, int $viewerId = 0): ?array
    {
        return Database::one(
            self::SELECT . ', (SELECT COUNT(*) FROM bookmarks b WHERE b.resource_id = r.id AND b.user_id = ?) AS is_bookmarked' .
            self::FROM . ' WHERE r.id = ?',
            [$viewerId, $id]
        );
    }

    /** Builds the shared WHERE for the listing and its counts. $skip leaves one filter out (for tab counts). */
    private static function filterSql(array $f, array $skip = []): array
    {
        $where = ["r.status = 'approved'"];
        $params = [];
        if (($f['q'] ?? '') !== '') {
            $where[] = '(r.title LIKE ? OR r.description LIKE ? OR s.code LIKE ? OR s.name LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like, $like, $like);
        }
        if (!empty($f['subjects']) && !in_array('subjects', $skip, true)) {
            $where[] = 'r.subject_id IN (' . implode(',', array_fill(0, count($f['subjects']), '?')) . ')';
            array_push($params, ...$f['subjects']);
        }
        if (($f['category'] ?? '') !== '' && !in_array('category', $skip, true)) {
            $where[] = 'c.slug = ?';
            $params[] = $f['category'];
        }
        if (!empty($f['year'])) {
            $where[] = 's.year = ?';
            $params[] = (int) $f['year'];
        }
        if (($f['faculty'] ?? '') !== '') {
            $where[] = 's.faculty = ?';
            $params[] = $f['faculty'];
        }
        if (($f['format'] ?? '') !== '') {
            $where[] = 'r.file_type = ?';
            $params[] = $f['format'];
        }
        if (!empty($f['min_rating'])) {
            $where[] = 'COALESCE(rt.rating, 0) >= ?';
            $params[] = (float) $f['min_rating'];
        }
        return [implode(' AND ', $where), $params];
    }

    public static function search(array $f, int $viewerId, int $page, int $perPage = 6): array
    {
        [$where, $params] = self::filterSql($f);
        $total = (int) Database::value('SELECT COUNT(*)' . self::FROM . " WHERE {$where}", $params);

        $order = match ($f['sort'] ?? 'relevance') {
            'newest'    => 'r.created_at DESC',
            'rating'    => 'rating DESC, rating_count DESC, r.id DESC',
            'downloads' => 'r.downloads_count DESC, r.id DESC',
            default     => '(COALESCE(rt.rating, 0) * LOG10(r.downloads_count + 10)) DESC, r.id DESC',
        };
        $offset = ($page - 1) * $perPage;
        $rows = Database::all(
            self::SELECT . ', (SELECT COUNT(*) FROM bookmarks b WHERE b.resource_id = r.id AND b.user_id = ?) AS is_bookmarked' .
            self::FROM . " WHERE {$where} ORDER BY {$order} LIMIT {$perPage} OFFSET {$offset}",
            array_merge([$viewerId], $params)
        );
        return ['rows' => $rows, 'total' => $total, 'pages' => (int) ceil($total / $perPage), 'page' => $page];
    }

    /** Count per category slug, respecting every other active filter. */
    public static function categoryCounts(array $f): array
    {
        [$where, $params] = self::filterSql($f, ['category']);
        $rows = Database::all('SELECT c.slug, COUNT(*) AS total' . self::FROM . " WHERE {$where} GROUP BY c.slug", $params);
        return array_column($rows, 'total', 'slug');
    }

    /** Count per subject, respecting every other active filter. */
    public static function subjectCounts(array $f): array
    {
        [$where, $params] = self::filterSql($f, ['subjects']);
        $rows = Database::all('SELECT r.subject_id, COUNT(*) AS total' . self::FROM . " WHERE {$where} GROUP BY r.subject_id", $params);
        return array_column($rows, 'total', 'subject_id');
    }

    public static function create(array $d): int
    {
        return Database::insert(
            'INSERT INTO resources (user_id, subject_id, category_id, title, description, file_path, original_name, file_type, file_size, pages)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $d['user_id'], $d['subject_id'], $d['category_id'], $d['title'], $d['description'],
                $d['file_path'], $d['original_name'], $d['file_type'], $d['file_size'], $d['pages'],
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM resources WHERE id = ?', [$id]);
    }

    public static function topRated(int $limit = 5, ?string $faculty = null): array
    {
        $where = $faculty !== null ? ' AND s.faculty = ?' : '';
        return Database::all(
            self::SELECT . self::FROM . " WHERE r.status = 'approved'{$where} ORDER BY rating DESC, rating_count DESC LIMIT " . (int) $limit,
            $faculty !== null ? [$faculty] : []
        );
    }

    public static function related(array $resource, int $limit = 3): array
    {
        return Database::all(
            self::SELECT . self::FROM . " WHERE r.status = 'approved' AND r.subject_id = ? AND r.id <> ?
             ORDER BY rating DESC LIMIT " . (int) $limit,
            [$resource['subject_id'], $resource['id']]
        );
    }

    public static function byUser(int $userId, bool $approvedOnly = false, ?string $faculty = null): array
    {
        $extra = $approvedOnly ? " AND r.status = 'approved'" : '';
        $params = [$userId];
        if ($faculty !== null) {
            $extra .= ' AND s.faculty = ?';
            $params[] = $faculty;
        }
        return Database::all(self::SELECT . self::FROM . " WHERE r.user_id = ?{$extra} ORDER BY r.created_at DESC", $params);
    }

    public static function bookmarked(int $userId, ?string $faculty = null): array
    {
        $where = $faculty !== null ? ' AND s.faculty = ?' : '';
        return Database::all(
            self::SELECT . ', 1 AS is_bookmarked' . self::FROM .
            " JOIN bookmarks b ON b.resource_id = r.id AND b.user_id = ? WHERE r.status = 'approved'{$where} ORDER BY b.created_at DESC",
            $faculty !== null ? [$userId, $faculty] : [$userId]
        );
    }

    public static function pending(): array
    {
        return Database::all(self::SELECT . self::FROM . " WHERE r.status = 'pending' ORDER BY r.created_at ASC");
    }

    public static function setStatus(int $id, string $status, int $adminId, ?string $reason = null): void
    {
        Database::run(
            'UPDATE resources SET status = ?, reject_reason = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?',
            [$status, $reason, $adminId, $id]
        );
    }

    /** Toggle and return the new state. */
    public static function toggleBookmark(int $userId, int $resourceId): bool
    {
        $deleted = Database::run('DELETE FROM bookmarks WHERE user_id = ? AND resource_id = ?', [$userId, $resourceId])->rowCount();
        if ($deleted > 0) {
            return false;
        }
        Database::run('INSERT INTO bookmarks (user_id, resource_id) VALUES (?, ?)', [$userId, $resourceId]);
        return true;
    }

    /** Owners and admins see everything. Others only see approved notes from their own faculty. */
    public static function canView(array $resource, ?array $viewer): bool
    {
        if ($viewer === null) {
            return false;
        }
        if ($viewer['role'] === 'admin' || (int) $viewer['id'] === (int) $resource['user_id']) {
            return true;
        }
        return $resource['status'] === 'approved' && $viewer['faculty'] === $resource['subject_faculty'];
    }
}
