<?php
/**
 * app/Services/StatsService.php  -  numbers for the dashboard and landing page.
 * Each *Live()/landing() method returns one payload, used for both the first page render and the
 * JSON polling, so the two can never disagree. Pass $faculty to limit numbers to one faculty.
 */
class StatsService
{
    private const LANDING_CACHE_SECONDS = 15;

    /** Chart ranges on the dashboard, with the label shown beside the chart title. */
    public const RANGES = ['week' => 'WEEKLY SPRINT', 'month' => 'LAST 4 WEEKS', 'half' => 'LAST 6 MONTHS'];

    /** '+12.5%' comparing the last 7 days with the 7 days before. */
    private static function trend(string $table, string $where, array $params = []): array
    {
        $now = (int) Database::value("SELECT COUNT(*) FROM {$table} WHERE {$where} AND created_at >= NOW() - INTERVAL 7 DAY", $params);
        $before = (int) Database::value(
            "SELECT COUNT(*) FROM {$table} WHERE {$where} AND created_at >= NOW() - INTERVAL 14 DAY AND created_at < NOW() - INTERVAL 7 DAY",
            $params
        );
        if ($before === 0) {
            return ['label' => $now > 0 ? '+' . $now . ' new' : '0%', 'down' => false];
        }
        $change = round(($now - $before) / $before * 100, 1);
        return ['label' => ($change >= 0 ? '+' : '') . $change . '%', 'down' => $change < 0];
    }

    /** SQL + params limiting a table with a subject_id column to one faculty. */
    private static function subjectFilter(?string $faculty): array
    {
        return $faculty === null ? ['', []] : [' AND subject_id IN (SELECT id FROM subjects WHERE faculty = ?)', [$faculty]];
    }

    /** Numbers about notes and questions, shared by dashboard and landing page. */
    private static function coreStats(?string $faculty): array
    {
        [$subjectSql, $params] = self::subjectFilter($faculty);

        $notes = (int) Database::value("SELECT COUNT(*) FROM resources WHERE status = 'approved'{$subjectSql}", $params);
        $questions = (int) Database::value("SELECT COUNT(*) FROM questions WHERE is_hidden = 0{$subjectSql}", $params);
        $answered = (int) Database::value(
            "SELECT COUNT(*) FROM questions WHERE is_hidden = 0{$subjectSql}
             AND EXISTS (SELECT 1 FROM answers a WHERE a.question_id = questions.id AND a.is_hidden = 0)",
            $params
        );
        $resolved = array_map('intval', array_column(Database::all(
            "SELECT TIMESTAMPDIFF(MINUTE, created_at, solved_at) AS mins FROM questions
             WHERE solved_at IS NOT NULL AND is_hidden = 0{$subjectSql} ORDER BY mins",
            $params
        ), 'mins'));

        $answerWhere = 'is_hidden = 0';
        if ($faculty !== null) {
            $answerWhere .= ' AND question_id IN (SELECT q.id FROM questions q JOIN subjects s ON s.id = q.subject_id WHERE s.faculty = ?)';
        }

        return [
            'notes' => $notes,
            'notesTrend' => self::trend('resources', "status = 'approved'{$subjectSql}", $params),
            'answered' => $answered,
            'answersTrend' => self::trend('answers', $answerWhere, $params),
            'medianMinutes' => $resolved ? $resolved[intdiv(count($resolved), 2)] : 0,
            'solvedPercent' => $questions ? (int) round(count($resolved) / $questions * 100) : 0,
        ];
    }

    /** Everything on the dashboard that updates by itself. */
    public static function dashboardLive(array $user, ?string $faculty, string $range = 'week'): array
    {
        $core = self::coreStats($faculty);
        [$subjectSql, $params] = self::subjectFilter($faculty);
        $modules = (int) Database::value("SELECT COUNT(DISTINCT subject_id) FROM resources WHERE status = 'approved'{$subjectSql}", $params);

        $studentWhere = "role = 'student' AND status = 'active'" . ($faculty !== null ? ' AND faculty = ?' : '');
        $facultyParam = $faculty !== null ? [$faculty] : [];
        $students = (int) Database::value("SELECT COUNT(*) FROM users WHERE {$studentWhere} AND last_seen_at >= NOW() - INTERVAL 30 DAY", $facultyParam);
        $liveNow = (int) Database::value("SELECT COUNT(*) FROM study_sessions WHERE starts_at <= NOW() AND COALESCE(ends_at, starts_at + INTERVAL 1 HOUR) >= NOW(){$subjectSql}", $params);
        $online = User::onlineCount($faculty);
        $goal = self::goal($user);
        $range = isset(self::RANGES[$range]) ? $range : 'week';
        $activity = self::activity($faculty, $range);

        return [
            'values' => [
                'notes' => $core['notes'],
                'students' => $students,
                'answered' => $core['answered'],
                'online' => $online,
                'streak' => $goal['streak'],
                'goalPercent' => $goal['percent'],
            ],
            'tags' => [
                'notes' => $core['notesTrend'],
                'students' => self::trend('users', "role = 'student'" . ($faculty !== null ? ' AND faculty = ?' : ''), $facultyParam),
                'answered' => $core['answersTrend'],
            ],
            'texts' => [
                'notesFoot' => 'Active peer vaults across ' . $modules . ' ' . ($modules === 1 ? 'module' : 'modules'),
                'studentsFoot' => $liveNow > 0 ? $liveNow . ' live ' . ($liveNow === 1 ? 'meeting' : 'meetings') . ' now' : 'Active this month',
                'answeredFoot' => 'Median resolution: ' . format_minutes($core['medianMinutes']) . ' · ' . $core['solvedPercent'] . '% Solved',
                'onlineWord' => $online === 1 ? 'peer' : 'peers',
                'streakWord' => $goal['streak'] === 1 ? 'day' : 'days',
                'goalHours' => format_minutes($goal['minutes']) . ' of ' . format_minutes($goal['target']) . ' target',
                'chartBadge' => self::RANGES[$range],
            ],
            'widths' => ['goal' => $goal['percent']],
            'bars' => ['weekly' => $activity],
            'goal' => $goal,
        ];
    }

    /** Notes and answers over time, oldest first. $range is one of RANGES. */
    private static function activity(?string $faculty, string $range): array
    {
        $buckets = self::buckets($range);
        $from = $buckets[0]['from'];
        [$subjectSql, $params] = self::subjectFilter($faculty);
        $notes = array_column(Database::all(
            "SELECT DATE(created_at) AS d, COUNT(*) AS c FROM resources
             WHERE status = 'approved' AND created_at >= ?{$subjectSql} GROUP BY d",
            array_merge([$from], $params)
        ), 'c', 'd');
        $answerFilter = $faculty !== null ? ' AND a.question_id IN (SELECT q.id FROM questions q JOIN subjects s ON s.id = q.subject_id WHERE s.faculty = ?)' : '';
        $answers = array_column(Database::all(
            "SELECT DATE(a.created_at) AS d, COUNT(*) AS c FROM answers a
             WHERE a.is_hidden = 0 AND a.created_at >= ?{$answerFilter} GROUP BY d",
            array_merge([$from], $params)
        ), 'c', 'd');

        $sum = function (array $counts, array $bucket): int {
            $total = 0;
            foreach ($counts as $day => $count) {
                if ($day >= $bucket['from'] && $day <= $bucket['to']) {
                    $total += (int) $count;
                }
            }
            return $total;
        };
        foreach ($buckets as &$bucket) {
            $bucket['notes'] = $sum($notes, $bucket);
            $bucket['answers'] = $sum($answers, $bucket);
        }
        unset($bucket);

        $totals = array_map(fn ($b) => $b['notes'] + $b['answers'], $buckets);
        $max = max($totals);
        $scale = $max > 0 ? 90 / $max : 0;   // keep headroom for the tooltip
        $peak = array_search($max, $totals, true);
        $sorted = $totals;
        sort($sorted);
        $median = $sorted[intdiv(count($sorted), 2)];

        $items = [];
        foreach ($buckets as $i => $b) {
            $isPeak = $i === $peak && $max > 0;
            $items[] = [
                'label' => $b['label'],
                'detail' => $b['detail'],
                'a' => $b['notes'],
                'b' => $b['answers'],
                'top' => round($b['notes'] * $scale, 1),
                'bottom' => round($b['answers'] * $scale, 1),
                'active' => $isPeak,
                'tooltip' => $isPeak ? "Peak sprint: {$b['notes']} notes / {$b['answers']} answers" : '',
                'title' => "{$b['detail']}: {$b['notes']} notes, {$b['answers']} answers",
            ];
        }
        return [
            'items' => $items,
            'median' => round($median * $scale, 1),
            'medianValue' => $median,
            'medianLabel' => '',
            'series' => ['Notes shared', 'Answers given'],
            'isEmpty' => $max === 0,
        ];
    }

    /** The time slots for each chart range, as inclusive date strings. */
    private static function buckets(string $range): array
    {
        $today = new DateTimeImmutable('today');
        $buckets = [];
        if ($range === 'month') {
            for ($w = 3; $w >= 0; $w--) {
                $end = $today->modify('-' . ($w * 7) . ' days');
                $start = $end->modify('-6 days');
                $buckets[] = ['from' => $start->format('Y-m-d'), 'to' => $end->format('Y-m-d'), 'label' => $start->format('M j'), 'detail' => $start->format('M j') . ' – ' . $end->format('M j')];
            }
        } elseif ($range === 'half') {
            $first = new DateTimeImmutable('first day of this month');
            for ($m = 5; $m >= 0; $m--) {
                $start = $first->modify("-{$m} months");
                $buckets[] = ['from' => $start->format('Y-m-d'), 'to' => $start->format('Y-m-t'), 'label' => $start->format('M'), 'detail' => $start->format('F Y')];
            }
        } else {
            for ($d = 6; $d >= 0; $d--) {
                $day = $today->modify("-{$d} days");
                $buckets[] = ['from' => $day->format('Y-m-d'), 'to' => $day->format('Y-m-d'), 'label' => $day->format('D'), 'detail' => $day->format('D, M j')];
            }
        }
        return $buckets;
    }

    /** Average rating of notes per subject shown as a percentage, best first. */
    public static function subjectMastery(int $limit = 4, ?string $faculty = null): array
    {
        $where = $faculty !== null ? ' AND s.faculty = ?' : '';
        return Database::all(
            "SELECT s.id, s.code, s.name, ROUND(AVG(rt.value) * 20) AS score
             FROM ratings rt JOIN resources r ON r.id = rt.resource_id JOIN subjects s ON s.id = r.subject_id
             WHERE r.status = 'approved'{$where} GROUP BY s.id ORDER BY score DESC, s.code LIMIT " . (int) $limit,
            $faculty !== null ? [$faculty] : []
        );
    }

    /** Weekly goal progress for one student. */
    public static function goal(array $user): array
    {
        $monday = date('Y-m-d', strtotime('monday this week'));
        $today = date('Y-m-d');
        $minutes = StudyLog::minutes((int) $user['id'], $monday, $today);
        $target = max(1, (int) $user['weekly_goal_minutes']);

        // Same days of last week, so the comparison is fair mid-week.
        $lastMonday = date('Y-m-d', strtotime($monday . ' -7 days'));
        $lastToday = date('Y-m-d', strtotime($today . ' -7 days'));
        $difference = $minutes - StudyLog::minutes((int) $user['id'], $lastMonday, $lastToday);

        return [
            'minutes' => $minutes,
            'target' => $target,
            'percent' => min(100, (int) round($minutes / $target * 100)),
            'pace' => $difference,
            'streak' => StudyLog::streak((int) $user['id']),
        ];
    }

    /** Landing payload, cached briefly because anyone can request it without signing in. */
    public static function landingCached(): array
    {
        $file = BASE_PATH . '/storage/cache/landing-stats.json';
        if (is_file($file) && filemtime($file) > time() - self::LANDING_CACHE_SECONDS) {
            $cached = json_decode((string) file_get_contents($file), true);
            if (is_array($cached)) {
                return $cached;
            }
        }
        $data = self::landing();
        @file_put_contents($file, json_encode($data), LOCK_EX);
        return $data;
    }

    /** Real totals across every faculty. Totals and subject names only, never people. */
    public static function landing(): array
    {
        $core = self::coreStats(null);
        $students = (int) Database::value("SELECT COUNT(*) FROM users WHERE role = 'student' AND status = 'active'");
        $active24 = (int) Database::value("SELECT COUNT(*) FROM users WHERE role = 'student' AND status = 'active' AND last_seen_at >= NOW() - INTERVAL 24 HOUR");
        $groups = (int) Database::value('SELECT COUNT(*) FROM study_groups');
        $meetings = (int) Database::value('SELECT COUNT(*) FROM study_sessions WHERE starts_at >= NOW()');
        $faculties = (int) Database::value("SELECT COUNT(DISTINCT faculty) FROM users WHERE role = 'student' AND status = 'active'");
        $modules = (int) Database::value("SELECT COUNT(DISTINCT subject_id) FROM resources WHERE status = 'approved'");
        $reviewed = (int) Database::value("SELECT COUNT(*) FROM resources WHERE status IN ('approved', 'rejected')");
        $approved = (int) Database::value("SELECT COUNT(*) FROM resources WHERE status = 'approved'");
        $acceptance = $reviewed ? round($approved / $reviewed * 100, 1) : 0;

        return [
            'values' => [
                'notes' => $core['notes'],
                'activeStudents' => $active24,
                'answered' => $core['answered'],
                'groups' => $groups,
                'students' => $students,
                'faculties' => $faculties,
            ],
            'tags' => [
                'notes' => $core['notesTrend'],
                'activeStudents' => self::trend('users', "role = 'student'"),
                'answered' => $core['answersTrend'],
            ],
            'texts' => [
                'notesFoot' => $modules . ' ' . ($modules === 1 ? 'module' : 'modules') . ' covered',
                'answeredFootA' => 'Median response: ' . format_minutes($core['medianMinutes']),
                'answeredFootB' => $core['solvedPercent'] . '% Solved',
                'groupsFoot' => $meetings . ' upcoming ' . ($meetings === 1 ? 'meeting' : 'meetings'),
                'acceptance' => $acceptance . '% verified',
                'facultyWord' => $faculties === 1 ? 'faculty' : 'faculties',
            ],
            'bars' => ['monthly' => self::monthlyNotes()],
            'heat' => self::heatmap(),
            'bubbles' => self::topSubjects($core['notes']),
        ];
    }

    /** Notes per month for the last 10 months, oldest first. */
    private static function monthlyNotes(): array
    {
        $rows = Database::all(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, status, COUNT(*) AS c FROM resources
             WHERE status IN ('approved', 'pending') AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 9 MONTH
             GROUP BY ym, status"
        );
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['ym']][$row['status']] = (int) $row['c'];
        }

        $months = [];
        $first = new DateTimeImmutable('first day of this month');
        for ($i = 9; $i >= 0; $i--) {
            $month = $first->modify("-{$i} months");
            $key = $month->format('Y-m');
            $months[] = ['label' => $month->format('M'), 'detail' => $month->format('F Y'), 'approved' => $counts[$key]['approved'] ?? 0, 'pending' => $counts[$key]['pending'] ?? 0];
        }
        $totals = array_map(fn ($m) => $m['approved'] + $m['pending'], $months);
        $max = max($totals);
        $scale = $max > 0 ? 90 / $max : 0;
        $peak = array_search($max, $totals, true);
        $average = round(array_sum($totals) / 10, 1);

        $items = [];
        foreach ($months as $i => $m) {
            $isPeak = $i === $peak && $max > 0;
            $items[] = [
                'label' => $m['label'],
                'detail' => $m['detail'],
                'a' => $m['approved'],
                'b' => $m['pending'],
                'top' => round($m['approved'] * $scale, 1),
                'bottom' => round($m['pending'] * $scale, 1),
                'active' => $isPeak,
                'tooltip' => $isPeak ? "{$m['approved']} notes (Peak) / {$m['pending']} under review" : '',
                'title' => "{$m['label']}: {$m['approved']} verified, {$m['pending']} under review",
            ];
        }
        return [
            'items' => $items,
            'median' => round($average * $scale, 1),
            'medianValue' => $average,
            'medianLabel' => 'Avg: ' . $average . ' notes',
            'series' => ['Verified notes', 'Under review'],
            'isEmpty' => $max === 0,
        ];
    }

    /** Activity per day for the last four Monday-to-Sunday weeks, as levels 0 to 3. */
    private static function heatmap(): array
    {
        $start = (new DateTimeImmutable('monday this week'))->modify('-21 days')->format('Y-m-d');
        $rows = Database::all(
            "SELECT d, SUM(c) AS c FROM (
                SELECT DATE(created_at) AS d, COUNT(*) AS c FROM resources WHERE status = 'approved' AND created_at >= ? GROUP BY d
                UNION ALL SELECT DATE(created_at), COUNT(*) FROM answers WHERE is_hidden = 0 AND created_at >= ? GROUP BY 1
                UNION ALL SELECT DATE(created_at), COUNT(*) FROM questions WHERE is_hidden = 0 AND created_at >= ? GROUP BY 1
                UNION ALL SELECT DATE(created_at), COUNT(*) FROM downloads WHERE created_at >= ? GROUP BY 1
             ) activity GROUP BY d",
            [$start, $start, $start, $start]
        );
        $counts = array_map('intval', array_column($rows, 'c', 'd'));
        $max = $counts ? max($counts) : 0;

        $cells = [];
        $cursor = new DateTimeImmutable($start);
        for ($i = 0; $i < 28; $i++) {
            $count = $counts[$cursor->format('Y-m-d')] ?? 0;
            $cells[] = [
                'level' => $count === 0 ? 0 : (int) ceil($count / $max * 3),
                'title' => $cursor->format('M j') . ': ' . $count . ' ' . ($count === 1 ? 'activity' : 'activities'),
            ];
            $cursor = $cursor->modify('+1 day');
        }
        return ['cells' => $cells, 'peak' => $max . ' on peak day'];
    }

    /** The three subjects with the most approved notes. */
    private static function topSubjects(int $totalNotes): array
    {
        $rows = Database::all(
            "SELECT s.name, COUNT(*) AS c FROM resources r JOIN subjects s ON s.id = r.subject_id
             WHERE r.status = 'approved' GROUP BY s.id ORDER BY c DESC, s.name LIMIT 3"
        );
        $modules = (int) Database::value("SELECT COUNT(DISTINCT subject_id) FROM resources WHERE status = 'approved'");
        return [
            'items' => array_map(fn ($r) => ['name' => $r['name'], 'percent' => $totalNotes ? (int) round($r['c'] / $totalNotes * 100) : 0], $rows),
            'note' => 'Share of all notes, across ' . $modules . ' ' . ($modules === 1 ? 'module' : 'modules'),
        ];
    }
}
