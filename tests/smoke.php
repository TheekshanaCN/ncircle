<?php
/**
 * tests/smoke.php  -  end-to-end test over real HTTP.
 *
 *   php -S 127.0.0.1:8770 -t public public/index.php     (in one terminal, APP_DEBUG=true)
 *   php tests/smoke.php http://127.0.0.1:8770            (in another)
 *
 * It creates throwaway accounts and content, so point it at a test database, never your real one:
 *   DB_NAME=ncircle_test php -S 127.0.0.1:8770 -t public public/index.php
 */
$base = rtrim($argv[1] ?? 'http://127.0.0.1:8770', '/');
$tmp = sys_get_temp_dir() . '/ncircle-smoke-' . getmypid();
mkdir($tmp);
$pass = 0;
$fail = [];

function check(string $name, bool $ok, string $detail = ''): void
{
    global $pass, $fail;
    if ($ok) {
        $pass++;
    } else {
        $fail[] = $name . ($detail !== '' ? " [{$detail}]" : '');
        echo "  FAIL {$name}" . ($detail !== '' ? " [{$detail}]" : '') . "\n";
    }
}

class Client
{
    public int $status = 0;
    public string $body = '';
    public string $location = '';
    private string $jar;

    public function __construct(string $name)
    {
        global $tmp;
        $this->jar = "{$tmp}/{$name}.jar";
    }

    public function request(string $method, string $path, $data = null, array $headers = []): self
    {
        global $base;
        $ch = curl_init(str_starts_with($path, 'http') ? $path : $base . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_COOKIEJAR => $this->jar,
            CURLOPT_COOKIEFILE => $this->jar,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $raw = (string) curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $head = substr($raw, 0, $size);
        $this->body = substr($raw, $size);
        $this->location = preg_match('/^Location: (.+)$/mi', $head, $m) ? trim($m[1]) : '';
        curl_close($ch);
        return $this;
    }

    public function get(string $path): self
    {
        return $this->request('GET', $path);
    }

    /** Fetches a token from $from, then POSTs. */
    public function post(string $path, array $data = [], string $from = '/login', bool $withToken = true): self
    {
        if ($withToken) {
            $this->get($from);
            preg_match('/name="csrf-token" content="([^"]+)"/', $this->body, $m);
            $data['_token'] = $m[1] ?? '';
        }
        global $base;
        return $this->request('POST', $path, $data, ['Referer: ' . $base . $from]);
    }

    public function follow(): self
    {
        return $this->location !== '' ? $this->get($this->location) : $this;
    }

    public function token(string $from): string
    {
        $this->get($from);
        preg_match('/name="csrf-token" content="([^"]+)"/', $this->body, $m);
        return $m[1] ?? '';
    }
}

function clean(Client $c, string $path): void
{
    $c->get($path);
    check("GET {$path} is 200", $c->status === 200, "got {$c->status}");
    check("GET {$path} has no PHP errors", !preg_match('/(Warning|Notice|Deprecated|Fatal error|Parse error|Uncaught)\b.*(on line|in \/)/', $c->body));
    check("GET {$path} has one main/title", substr_count($c->body, '<title>') === 1);
}

function lastMailLink(string $contains): string
{
    $log = (string) file_get_contents(dirname(__DIR__) . '/storage/logs/mail.log');
    preg_match_all('#https?://\S*' . preg_quote($contains, '#') . '\S*#', $log, $m);
    return (string) end($m[0]);
}

$stamp = substr((string) time(), -6);
$email = "smoke{$stamp}@students.nsbm.ac.lk";
$password = 'Smoke@1234';
$xss = '<script>alert(1)</script>';

echo "Public pages\n";
$guest = new Client('guest');
foreach (['/', '/login', '/register', '/forgot-password', '/terms', '/privacy', '/integrity', '/help', '/dev/styleguide'] as $p) {
    clean($guest, $p);
}
$guest->get('/nope');
check('unknown page is a styled 404', $guest->status === 404 && str_contains($guest->body, 'Page not found'));

echo "Access control for guests\n";
foreach (['/dashboard', '/resources', '/forum', '/groups', '/sessions', '/space', '/notifications', '/admin', '/resources/1/download'] as $p) {
    $guest->get($p);
    check("guest is redirected from {$p}", $guest->status === 302 && str_contains($guest->location, '/login'), "{$guest->status} {$guest->location}");
}

echo "CSRF\n";
$guest->post('/login', ['login' => 'admin@students.nsbm.ac.lk', 'password' => 'Admin@123'], '/login', false);
check('POST without a token is rejected', $guest->status === 302 && str_contains($guest->location, '/login') && !str_contains($guest->follow()->body, 'Sign out'));
$guest->get('/dashboard');
check('rejected login did not sign in', $guest->status === 302);

echo "Registration rules\n";
$good = ['name' => "Smoke Tester", 'student_id' => "9{$stamp}", 'email' => $email, 'faculty' => 'Computing', 'academic_year' => '2', 'password' => $password, 'password_confirmation' => $password, 'honor' => '1'];
$new = new Client('new');
$new->post('/register', array_merge($good, ['email' => 'someone@gmail.com']), '/register');
check('non-NSBM email is refused', str_contains($new->follow()->body, 'official NSBM domain'));
$new->post('/register', array_merge($good, ['password_confirmation' => 'Different1']), '/register');
check('password mismatch is refused', str_contains($new->follow()->body, 'Passwords do not match'));
$new->post('/register', array_merge($good, ['password' => 'weak', 'password_confirmation' => 'weak']), '/register');
check('weak password is refused', str_contains($new->follow()->body, 'uppercase letter'));
check('password is not echoed back', !str_contains($new->body, 'value="weak"'));
$new->post('/register', array_diff_key($good, ['honor' => 1]), '/register');
check('honor code is required', str_contains($new->follow()->body, 'Honor Code is required'));
$new->post('/register', array_merge($good, ['student_id' => '6942']), '/register');
check('taken student ID is refused', str_contains($new->follow()->body, 'already registered'));

$new->post('/register', $good, '/register');
check('valid sign-up goes to check inbox', str_contains($new->location, '/check-inbox'), $new->location);
$new->follow();
check('check inbox page shows the email', str_contains($new->body, $email));
$new->post('/login', ['login' => $email, 'password' => $password], '/check-inbox');
check('unverified account cannot sign in', str_contains($new->location, '/check-inbox') || str_contains($new->location, '/login'), $new->location);
$link = lastMailLink('/verify-email?token=');
check('verification email was written', $link !== '');
$new->get($link);
check('verification link works', str_contains($new->follow()->body, 'Email verified'));
$new->get($link);
check('verification link is single use', str_contains($new->follow()->body, 'invalid or has expired'));

echo "Sign in\n";
$new->post('/login', ['login' => $email, 'password' => 'Wrong@1234']);
check('wrong password shows the design error', str_contains($new->follow()->body, 'Incorrect email or password'));
$new->post('/login', ['login' => $email, 'password' => $password]);
check('correct password signs in', str_contains($new->location, '/dashboard'), $new->location);
$new->get('/login');
check('signed-in users skip the login page', $new->status === 302);

echo "Pages for a new student with little data\n";
foreach (['/dashboard', '/resources', '/resources/create', '/forum', '/forum/create', '/groups', '/groups/create', '/sessions', '/sessions/create', '/sessions?when=past', '/sessions?mode=physical', '/members', '/members/directory', '/space', '/profile/edit', '/notifications', '/search?q=database', '/search?q=zzzznothing', '/search', '/terms', '/help'] as $p) {
    clean($new, $p);
}
$new->get('/profile');
clean($new, $new->location);
$new->get('/admin');
check('students cannot open admin', $new->status === 403);
$new->post('/admin/users/2/status', [], '/dashboard');
check('students cannot use admin actions', $new->status === 403);

echo "Upload\n";
file_put_contents("{$tmp}/evil.pdf", "<?php echo 'hacked'; ?>");
file_put_contents("{$tmp}/evil.php", "<?php echo 'hacked'; ?>");
file_put_contents("{$tmp}/real.pdf", "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n");
$note = ['title' => "Smoke note {$xss}", 'subject_id' => '1', 'category_id' => '2', 'description' => "Smoke description {$xss} long enough", 'pages' => '3'];
$tok = $new->token('/resources/create');
$new->request('POST', '/resources', $note + ['_token' => $tok, 'file' => new CURLFile("{$tmp}/evil.php", 'application/pdf', 'evil.php')], ['Referer: ' . $base . '/resources/create']);
check('a .php upload is refused', str_contains($new->follow()->body, 'Only real PDF'));
$new->request('POST', '/resources', $note + ['_token' => $tok, 'file' => new CURLFile("{$tmp}/evil.pdf", 'application/pdf', 'evil.pdf')], ['Referer: ' . $base . '/resources/create']);
check('fake PDF content is refused', str_contains($new->follow()->body, 'Only real PDF'));
$new->request('POST', '/resources', $note + ['_token' => $tok, 'file' => new CURLFile("{$tmp}/real.pdf", 'application/pdf', 'real.pdf')], ['Referer: ' . $base . '/resources/create']);
check('a real PDF is accepted', (bool) preg_match('#/resources/(\d+)$#', $new->location, $m), $new->location);
$noteId = (int) ($m[1] ?? 0);
$new->follow();
check('note page escapes HTML', !str_contains($new->body, $xss) && str_contains($new->body, '&lt;script&gt;'));
check('uploader sees it is pending', str_contains($new->body, 'waiting for approval'));

echo "Second student\n";
$other = new Client('other');
$other->post('/login', ['login' => 'kavindu@students.nsbm.ac.lk', 'password' => 'Student@123']);
check('demo student signs in', str_contains($other->location, '/dashboard'), $other->location);
$other->get("/resources/{$noteId}");
check('pending note is hidden from others', $other->status === 404);
$other->get("/resources/{$noteId}/download");
check('pending file cannot be downloaded by others', $other->status === 404);
$other->post("/resources/{$noteId}/delete", [], '/dashboard');
check('others cannot delete a note', $other->status === 403);
$other->get('/resources?q=' . urlencode("' OR 1=1 -- "));
check('SQL in search is harmless', $other->status === 200 && str_contains($other->body, 'No notes match'));
$other->get('/resources?subject[]=' . urlencode('1 OR 1=1') . '&sort=' . urlencode('x;drop') . '&year=99&format=exe');
check('junk filters are ignored safely', $other->status === 200);
clean($other, '/resources?category=past-paper&year=2&format=pdf&min_rating=4&sort=downloads');
$other->get('/resources?page=2&partial=1');
check('load more returns cards only', $other->status === 200 && str_contains($other->body, 'sc-note-card') && !str_contains($other->body, '<html'));

echo "Admin review\n";
$admin = new Client('admin');
$admin->post('/login', ['login' => 'admin@students.nsbm.ac.lk', 'password' => 'Admin@123']);
check('admin lands on admin', str_contains($admin->location, '/admin'), $admin->location);
foreach (['/admin', '/admin/approvals', '/admin/moderation', '/admin/users', '/admin/users?q=perera', '/admin/subjects', '/admin/reports', "/resources/{$noteId}"] as $p) {
    clean($admin, $p);
}
$admin->post("/admin/approvals/{$noteId}/reject", ['reason' => 'x'], '/admin/approvals');
check('rejecting needs a reason', str_contains($admin->follow()->body, 'Give the uploader a reason'));
$admin->post("/admin/approvals/{$noteId}/approve", [], '/admin/approvals');
check('approve works', str_contains($admin->follow()->body, 'Approved:'));
check('flash message escapes HTML', !str_contains($admin->body, $xss));
$new->get('/notifications');
check('uploader is notified', str_contains($new->body, 'Your note was approved'));
$admin->get('/admin/reports/export');
check('CSV export works', $admin->status === 200 && str_contains($admin->body, 'Code,Subject'));

echo "Note interactions\n";
$other->get("/resources/{$noteId}");
check('approved note is now visible', $other->status === 200);
$tok = $other->token('/dashboard');
$rate = fn (Client $c, $value, string $token) => $c->request('POST', "/api/resources/{$noteId}/rate", json_encode(['value' => $value]), ['Content-Type: application/json', 'Accept: application/json', "X-CSRF-Token: {$token}"]);
$rate($other, 9, $tok);
check('rating out of range is refused', $other->status === 422, (string) $other->status);
$rate($other, 4, $tok);
check('rating saves', $other->status === 200 && json_decode($other->body, true)['data']['rating'] == 4, $other->body);
$rate($other, 5, $tok);
check('rating again updates, not duplicates', json_decode($other->body, true)['data']['rating_count'] === 1, $other->body);
$rate($new, 5, $new->token('/dashboard'));
check('owner cannot rate own note', $new->status === 403, (string) $new->status);
$other->request('POST', "/api/resources/{$noteId}/rate", json_encode(['value' => 5]), ['Content-Type: application/json', 'Accept: application/json']);
check('AJAX without CSRF is refused', $other->status === 419, (string) $other->status);
$other->get("/resources/{$noteId}/download");
check('download works', $other->status === 200 && str_starts_with($other->body, '%PDF'));
$other->get("/resources/{$noteId}");
check('download count went up', str_contains($other->body, '<dd>1</dd>'));
$other->post("/resources/{$noteId}/bookmark", [], '/dashboard');
$other->get('/space');
check('bookmark appears in study space', str_contains($other->body, 'Smoke note'));
$other->post("/resources/{$noteId}/comments", ['body' => "Nice {$xss}"], '/dashboard');
$other->get("/resources/{$noteId}");
check('comment is shown and escaped', str_contains($other->body, 'Nice &lt;script&gt;') && !str_contains($other->body, "Nice {$xss}"));
$other->post('/flag', ['target_type' => 'resource', 'target_id' => $noteId, 'reason' => 'Smoke test report'], "/resources/{$noteId}");
$admin->get('/admin/moderation');
check('report reaches moderation', str_contains($admin->body, 'Smoke test report'));
$other->post('/flag', ['target_type' => 'nonsense', 'target_id' => 1, 'reason' => 'Smoke test report'], '/dashboard');
check('bad report target is refused', $other->status === 404);

echo "Forum\n";
$new->post('/forum', ['title' => 'short', 'subject_id' => '1', 'body' => 'too short'], '/forum/create');
check('short question is refused', str_contains($new->follow()->body, 'at least 10'));
$new->post('/forum', ['title' => "A real smoke question {$xss}", 'subject_id' => '1', 'body' => 'Body of the smoke question.'], '/forum/create');
check('question is created', (bool) preg_match('#/forum/(\d+)$#', $new->location, $m), $new->location);
$qid = (int) $m[1];
clean($new, "/forum/{$qid}");
check('question is escaped', !str_contains($new->body, $xss));
$other->post("/forum/{$qid}/answers", ['body' => 'Here is a smoke answer.'], '/dashboard');
$other->get("/forum/{$qid}");
preg_match('#/answers/(\d+)/vote#', $other->body, $m);
$aid = (int) ($m[1] ?? 0);
check('answer appears', $aid > 0);
$new->get('/notifications');
check('asker is notified', str_contains($new->body, 'answered your question'));
$other->post("/answers/{$aid}/accept", [], '/dashboard');
check('only the asker can accept', $other->status === 403);
$new->post("/answers/{$aid}/vote", [], '/dashboard');
$new->post("/answers/{$aid}/accept", [], '/dashboard');
$new->get("/forum/{$qid}");
check('accepted answer is marked solved', str_contains($new->body, 'ACCEPTED') && str_contains($new->body, 'SOLVED IN'));
check('upvote counted', str_contains($new->body, '<strong>1</strong>'));
$other->post("/answers/{$aid}/vote", [], '/dashboard');
check('cannot upvote own answer', str_contains($other->follow()->body, 'cannot upvote your own'));
$other->post("/forum/{$qid}/delete", [], '/dashboard');
check('others cannot delete a question', $other->status === 403);

echo "Groups and pods\n";
$new->post('/groups', ['name' => 'Smoke Group', 'subject_id' => '1', 'description' => 'A group for smoke testing.', 'max_members' => '2'], '/groups/create');
check('group is created', (bool) preg_match('#/groups/(\d+)$#', $new->location, $m), $new->location);
$gid = (int) $m[1];
clean($new, "/groups/{$gid}");
$other->post("/groups/{$gid}/posts", ['body' => 'Not a member yet'], '/dashboard');
check('non-members cannot post', $other->status === 403);
$other->post("/groups/{$gid}/join", [], '/dashboard');
$other->get("/groups/{$gid}");
check('join works', str_contains($other->body, 'Leave group'));
$third = new Client('third');
$third->post('/login', ['login' => 'ravindu@students.nsbm.ac.lk', 'password' => 'Student@123']);
$third->post("/groups/{$gid}/join", [], '/dashboard');
check('a full group refuses joiners', str_contains($third->follow()->body, 'This group is full'));
$new->post("/groups/{$gid}/leave", [], '/dashboard');
check('owner cannot leave', str_contains($new->follow()->body, 'Delete it instead'));
$other->post("/groups/{$gid}/delete", [], '/dashboard');
check('members cannot delete the group', $other->status === 403);

$tomorrow = date('Y-m-d', time() + 86400);
$meeting = ['title' => 'Smoke pod', 'subject_id' => '1', 'mode' => 'online', 'date' => $tomorrow, 'start_time' => '10:00', 'end_time' => '11:00', 'capacity' => '2', 'description' => 'A smoke testing pod.', 'meeting_url' => 'https://meet.example.com/smoke-link'];
$new->post('/sessions', array_merge($meeting, ['date' => '2020-01-01']), '/sessions/create');
check('past start time is refused', str_contains($new->follow()->body, 'start in the future'));
$new->post('/sessions', array_merge($meeting, ['end_time' => '09:00']), '/sessions/create');
check('end before start is refused', str_contains($new->follow()->body, 'must be after the start'));
$new->post('/sessions', array_merge($meeting, ['mode' => 'physical', 'location' => '']), '/sessions/create');
check('physical meeting needs a place', str_contains($new->follow()->body, 'building and room'));
$new->post('/sessions', array_merge($meeting, ['meeting_url' => '']), '/sessions/create');
check('online meeting needs a link', str_contains($new->follow()->body, 'Add the meeting link'));
foreach (['http://meet.example.com/x', 'javascript:alert(1)'] as $badLink) {
    $new->post('/sessions', array_merge($meeting, ['meeting_url' => $badLink]), '/sessions/create');
    check("meeting link {$badLink} is refused", str_contains($new->follow()->body, 'starting with https://'));
}
$new->post('/sessions', $meeting, '/sessions/create');
check('meeting is created', str_contains($new->location, '/sessions'), $new->location);
$other->get('/sessions');
preg_match_all('#/sessions/(\d+)/join#', $other->body, $all);
$new->get('/sessions?mine=1');
preg_match('#/sessions/(\d+)/delete#', $new->body, $m);
$sid = (int) ($m[1] ?? 0);
check('host can see their pod', $sid > 0);
$other->post("/sessions/{$sid}/join", [], '/dashboard');
$third->post("/sessions/{$sid}/join", [], '/dashboard');
check('a full meeting refuses joiners', str_contains($third->follow()->body, 'This meeting is full'));
$third->get('/sessions');
check('meeting link is hidden until going', !str_contains($third->body, 'smoke-link'));
$other->get('/sessions');
check('meeting link shows when going', str_contains($other->body, 'smoke-link'));
$other->get("/sessions/{$sid}/edit");
check('attendees cannot edit a meeting', $other->status === 403);
$new->post("/sessions/{$sid}", array_merge($meeting, ['title' => 'Smoke pod renamed', 'capacity' => '3']), "/sessions/{$sid}/edit");
$third->post("/sessions/{$sid}/join", [], '/dashboard');
$new->post("/sessions/{$sid}", array_merge($meeting, ['capacity' => '2']), "/sessions/{$sid}/edit");
check('seats cannot drop below people going', str_contains($new->follow()->body, 'already going'));
$third->post("/sessions/{$sid}/leave", [], '/dashboard');
$other->get('/notifications');
check('people going hear about a change', str_contains($other->body, 'A meeting was updated'));
$other->post("/sessions/{$sid}/leave", [], '/dashboard');
$other->get('/sessions');
check('saying cannot go hides the link again', !str_contains($other->body, 'smoke-link'));
$other->post("/sessions/{$sid}/join", [], '/dashboard');
$other->post("/sessions/{$sid}/delete", [], '/dashboard');
check('attendees cannot cancel a meeting', $other->status === 403);
$new->post("/sessions/{$sid}/delete", [], '/dashboard');
$other->get('/notifications');
check('attendees hear about a cancellation', str_contains($other->body, 'was cancelled'));

echo "Faculty separation\n";
$biz = new Client('biz');
$biz->post('/login', ['login' => 'sachini@students.nsbm.ac.lk', 'password' => 'Student@123']);
check('business student signs in', str_contains($biz->location, '/dashboard'), $biz->location);
foreach (['/dashboard', '/resources', '/forum', '/groups', '/sessions', '/search?q=Normalization'] as $p) {
    clean($biz, $p);
    check("{$p} hides Computing content", !str_contains($biz->body, 'Normalization Cheat Sheet') && !str_contains($biz->body, 'Normalization Problem') && !str_contains($biz->body, 'CS204'));
}
$biz->get('/resources');
check('browse shows own faculty notes', str_contains($biz->body, 'BM201') || str_contains($biz->body, 'BM102'));
$biz->get('/resources?faculty=Computing');
check('faculty filter cannot be overridden', !str_contains($biz->body, 'CS204'));
$biz->get('/resources?subject[]=1');
check('subject filter cannot reach another faculty', !str_contains($biz->body, 'CS204'));
foreach (['/resources/1', '/resources/1/download', '/forum/1', '/groups/1'] as $p) {
    $biz->get($p);
    check("GET {$p} from another faculty is 404", $biz->status === 404, (string) $biz->status);
}
$posts = [
    '/resources/1/bookmark' => [], '/resources/1/comments' => ['body' => 'Sneaky comment'],
    '/forum/1/answers' => ['body' => 'Sneaky answer'], '/answers/1/vote' => [], '/answers/1/accept' => [],
    '/groups/1/join' => [], '/groups/1/posts' => ['body' => 'Sneaky post'], '/sessions/1/join' => [], '/sessions/1/leave' => [],
    '/flag' => ['target_type' => 'resource', 'target_id' => 1, 'reason' => 'Sneaky report'],
];
foreach ($posts as $p => $data) {
    $biz->post($p, $data, '/dashboard');
    check("POST {$p} from another faculty is 404", $biz->status === 404, (string) $biz->status);
}
$bizToken = $biz->token('/dashboard');
$biz->request('POST', '/api/resources/1/rate', json_encode(['value' => 5]), ['Content-Type: application/json', 'Accept: application/json', "X-CSRF-Token: {$bizToken}"]);
check('cannot rate another faculty note', $biz->status === 404, (string) $biz->status);
$biz->get('/resources/create');
check('upload only offers own subjects', str_contains($biz->body, 'BM201') && !str_contains($biz->body, 'CS204'));
$biz->post('/forum', ['title' => 'A question in the wrong faculty', 'subject_id' => '1', 'body' => 'This should be refused.'], '/forum/create');
check('cannot post into another faculty', str_contains($biz->follow()->body, 'Choose a valid subject'));
$biz->get('/profile/4');
check('profiles open across faculties', $biz->status === 200 && str_contains($biz->body, 'Kavindu Silva'));
check('profiles hide other faculty notes', !str_contains($biz->body, 'OOP Principles'));
$admin->get('/resources/1');
check('admins can see every faculty', $admin->status === 200);
$admin->get('/resources?q=Normalization');
check('admins can browse every faculty', str_contains($admin->body, 'Normalization'));

echo "Group meetings\n";
$nimesh = new Client('nimesh');
$nimesh->post('/login', ['login' => 'nimesh@students.nsbm.ac.lk', 'password' => 'Student@123']);
$nimesh->get('/groups/1');
check('members see group meetings', str_contains($nimesh->body, 'Database Normalization Problem Solving Sprint'));
check('group meeting shows the place', str_contains($nimesh->body, 'Faculty of Computing, Lab 04'));
check('group meeting link hidden before going', !str_contains($nimesh->body, 'ncircle-demo-a'));
$nimesh->post('/sessions/1/join', [], '/groups/1');
$nimesh->get('/groups/1');
check('group meeting link shows when going', str_contains($nimesh->body, 'ncircle-demo-a'));
$new->get('/sessions');
check('group meetings are hidden from non-members', !str_contains($new->body, 'Database Normalization Problem Solving Sprint'));
$new->post('/sessions/1/join', [], '/dashboard');
check('non-members cannot join a group meeting', $new->status === 404, (string) $new->status);
$new->get('/sessions/1/edit');
check('non-members cannot open a group meeting', $new->status === 404, (string) $new->status);
$nimesh->post('/sessions', ['group_id' => '1', 'title' => 'Group smoke meeting', 'mode' => 'physical', 'date' => $tomorrow, 'start_time' => '14:00', 'end_time' => '15:30', 'location' => 'Library Room 3', 'capacity' => '6', 'description' => 'A member schedules a meeting.'], '/sessions/create?group=1');
check('any member can schedule a group meeting', str_contains($nimesh->location, '/groups/1'), $nimesh->location);
$other->get('/notifications');
check('members are told about new meetings', str_contains($other->body, 'New meeting in'));
$tharushi = new Client('tharushi');
$tharushi->post('/login', ['login' => 'tharushi@students.nsbm.ac.lk', 'password' => 'Student@123']);
$tharushi->get('/groups/1');
preg_match_all('#/sessions/(\d+)/edit#', $tharushi->body, $edits);
check('group owner can manage members meetings', count($edits[1]) >= 2, (string) count($edits[1]));
$other->get('/groups/1');
check('ordinary members cannot manage others meetings', !preg_match('#/sessions/\d+/edit#', $other->body));
$new->post('/sessions', ['group_id' => '1', 'title' => 'Outsider group meeting', 'mode' => 'physical', 'date' => $tomorrow, 'start_time' => '14:00', 'end_time' => '15:00', 'location' => 'Somewhere', 'capacity' => '6', 'description' => 'Should be refused.'], '/sessions/create');
check('non-members cannot schedule group meetings', $new->status === 403, (string) $new->status);

echo "Find Members\n";
foreach (['/members', '/members/directory', '/members/directory?faculty=Business&open=1', '/members/requests/create', '/members?mine=1', '/members?type=research&faculty=Business'] as $p) {
    clean($biz, $p);
}
$biz->get('/members/directory');
check('directory shows every faculty', str_contains($biz->body, 'Faculty of Computing') && str_contains($biz->body, 'Faculty of Engineering'));
check('directory has no emails', !str_contains($biz->body, '@students.nsbm.ac.lk'));
$biz->post('/members/requests', ['title' => 'short', 'type' => 'research', 'description' => 'too short'], '/members/requests/create');
check('short request is refused', str_contains($biz->follow()->body, 'at least 10'));
$biz->post('/members/requests', ['title' => "Need a developer {$xss}", 'type' => 'research', 'description' => 'A long enough description of the research project.', 'skills' => 'PHP', 'faculties' => ['Computing', 'NotAFaculty']], '/members/requests/create');
check('request is created', (bool) preg_match('#/members/requests/(\d+)$#', $biz->location, $m), $biz->location);
$rid = (int) ($m[1] ?? 0);
clean($biz, "/members/requests/{$rid}");
check('request is escaped', !str_contains($biz->body, $xss));
check('fake faculty is dropped', !str_contains($biz->body, 'NotAFaculty'));
$biz->post("/members/requests/{$rid}/respond", ['message' => 'Responding to myself here.'], '/dashboard');
check('cannot respond to your own request', $biz->status === 403, (string) $biz->status);
$other->get("/members/requests/{$rid}");
check('other faculties can open requests', $other->status === 200);
check('responder cannot see an email yet', !str_contains($other->body, 'sachini@students'));
$other->post("/members/requests/{$rid}/respond", ['message' => 'I can build this for you in PHP.'], '/dashboard');
$other->post("/members/requests/{$rid}/respond", ['message' => 'A second response from the same person.'], '/dashboard');
$biz->get('/notifications');
check('poster is told about interest', str_contains($biz->body, 'is interested in your request'));
$biz->get("/members/requests/{$rid}");
check('poster sees one response per person', substr_count($biz->body, '/accept') === 1, (string) substr_count($biz->body, '/accept'));
check('poster cannot see an email before accepting', !str_contains($biz->body, 'kavindu@students'));
preg_match('#/members/responses/(\d+)/accept#', $biz->body, $m);
$respId = (int) ($m[1] ?? 0);
$third->get("/members/requests/{$rid}");
check('others cannot read responses', !str_contains($third->body, 'I can build this'));
$other->post("/members/responses/{$respId}/accept", [], '/dashboard');
check('responder cannot accept themselves', $other->status === 403, (string) $other->status);
$biz->post("/members/responses/{$respId}/accept", [], "/members/requests/{$rid}");
$biz->get("/members/requests/{$rid}");
check('after accept poster sees responder email', str_contains($biz->body, 'kavindu@students.nsbm.ac.lk'));
$other->get("/members/requests/{$rid}");
check('after accept responder sees poster email', str_contains($other->body, 'sachini@students.nsbm.ac.lk'));
$third->get("/members/requests/{$rid}");
check('a third person still sees no emails', !str_contains($third->body, '@students.nsbm.ac.lk'));
$admin->get("/members/requests/{$rid}");
check('admins moderate without emails', !str_contains($admin->body, 'kavindu@students') && !str_contains($admin->body, 'sachini@students'));
$other->get("/profile/" . 6);
check('accepting does not expose email on profiles', !str_contains($other->body, 'sachini@students'));
$other->post("/members/requests/{$rid}/close", [], '/dashboard');
check('only the poster can close', $other->status === 403, (string) $other->status);
$biz->post("/members/requests/{$rid}/close", [], "/members/requests/{$rid}");
$third->post("/members/requests/{$rid}/respond", ['message' => 'Too late to respond here.'], '/dashboard');
check('closed requests refuse responses', str_contains($third->follow()->body, 'This request is closed'));
$third->post('/flag', ['target_type' => 'help_request', 'target_id' => $rid, 'reason' => 'Smoke report on a request'], '/dashboard');
$admin->get('/admin/moderation');
check('request reports reach moderation', str_contains($admin->body, 'Smoke report on a request'));

echo "Sharing images and files\n";
$png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
$fixture = function (string $name, string $data) use ($tmp): string {
    file_put_contents("{$tmp}/{$name}", $data);
    return "{$tmp}/{$name}";
};
$office = function (string $name, string $required, array $extra = []) use ($tmp): string {
    $zip = new ZipArchive();
    $zip->open("{$tmp}/{$name}", ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFromString('[Content_Types].xml', '<Types/>');
    $zip->addFromString($required, '<doc/>');
    foreach ($extra as $entry) {
        $zip->addFromString($entry, 'x');
    }
    $zip->close();
    return "{$tmp}/{$name}";
};
$share = function (Client $c, string $action, string $from, array $files, array $fields = []) use ($base): Client {
    $data = $fields + ['_token' => $c->token($from)];
    foreach ($files as $i => [$path, $name]) {
        $data["attachments[{$i}]"] = new CURLFile($path, 'application/octet-stream', $name);
    }
    return $c->request('POST', $action, $data, ['Referer: ' . $base . $from]);
};
$bad = [
    'program named .exe' => [$fixture('a.exe', "MZ\x90\x00"), 'virus.exe'],
    'PHP script' => [$fixture('a.php', '<?php echo 1;'), 'shell.php'],
    'double extension' => [$fixture('b.pdf', "%PDF-1.4\n%%EOF"), 'shell.php.pdf'],
    'hidden program' => [$fixture('c.txt', 'hello'), 'virus.exe.txt'],
    'script as image' => [$fixture('d.png', '<?php echo 1;'), 'photo.png'],
    'program as image' => [$fixture('e.jpg', "MZ\x90\x00program"), 'photo.jpg'],
    'PNG named JPG' => [$fixture('f.jpg', $png), 'photo.jpg'],
    'SVG image' => [$fixture('g.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'), 'logo.svg'],
    'HTML page' => [$fixture('h.html', '<script>alert(1)</script>'), 'page.html'],
    'HTML named PDF' => [$fixture('i.pdf', '<html><script>alert(1)</script></html>'), 'notes.pdf'],
    'ZIP archive' => [$office('j.zip', 'a.txt'), 'archive.zip'],
    'PDF with JavaScript' => [$fixture('k.pdf', "%PDF-1.4\n1 0 obj<</S/JavaScript/JS(app.alert(1))>>endobj\n%%EOF"), 'notes.pdf'],
    'PDF hiding JavaScript' => [$fixture('l.pdf', "%PDF-1.4\n1 0 obj<</S/J#61vaScript>>endobj\n%%EOF"), 'notes.pdf'],
    'PDF that launches programs' => [$fixture('m.pdf', "%PDF-1.4\n1 0 obj<</S/Launch/F(cmd.exe)>>endobj\n%%EOF"), 'notes.pdf'],
    'Word file with macros' => [$office('n.docx', 'word/document.xml', ['word/vbaProject.bin']), 'notes.docx'],
    'ZIP named Word file' => [$office('o.docx', 'readme.txt'), 'notes.docx'],
    'Excel named Word file' => [$office('p.docx', 'xl/workbook.xml'), 'notes.docx'],
    'program inside PowerPoint' => [$office('q.pptx', 'ppt/presentation.xml', ['ppt/embeddings/oleObject1.bin']), 'slides.pptx'],
    'binary named text' => [$fixture('r.txt', "abc\0def"), 'notes.txt'],
    'macro Word extension' => [$fixture('s.docm', 'x'), 'notes.docm'],
    'oversized image' => [$fixture('t.png', $png . str_repeat("\0", 6 * 1024 * 1024)), 'big.png'],
];
foreach ($bad as $label => $file) {
    $share($nimesh, '/groups/1/posts', '/groups/1', [$file], ['body' => "Should be refused {$label}"]);
    $page = $nimesh->follow()->body;
    check("{$label} is refused", str_contains($page, 'sc-field__error') && !str_contains($page, "<p class=\"sc-post__text\">Should be refused {$label}"), $label);
}
$goodFiles = [[$fixture('ok.png', $png), 'diagram.png'], [$office('ok.docx', 'word/document.xml'), 'my notes.docx'], [$office('ok.xlsx', 'xl/workbook.xml'), 'marks.xlsx'], [$fixture('ok.txt', "Plain notes ✅\n"), 'notes.txt']];
$share($nimesh, '/groups/1/posts', '/groups/1', array_merge($goodFiles, [[$tmp . '/real.pdf', 'real.pdf']]), ['body' => 'Too many files']);
check('more than 4 files is refused', str_contains($nimesh->follow()->body, 'up to 4 files'));
$share($nimesh, '/groups/1/posts', '/groups/1', $goodFiles, ['body' => "Shared files with emoji 🎉📚 {$xss}"]);
$nimesh->follow();
check('good files are accepted', str_contains($nimesh->body, 'diagram.png') && str_contains($nimesh->body, 'my notes.docx') && str_contains($nimesh->body, 'marks.xlsx'));
check('emoji survive saving', str_contains($nimesh->body, '🎉📚'));
check('post text stays escaped', !str_contains($nimesh->body, $xss));
preg_match_all('#/attachments/(\d+)"[^>]*(data-lightbox|download)#', $nimesh->body, $found);
$imageId = 0;
$fileId = 0;
foreach ($found[1] as $i => $aid) {
    if ($found[2][$i] === 'data-lightbox') { $imageId = (int) $aid; } elseif (!$fileId) { $fileId = (int) $aid; }
}
check('attachment links are shown', $imageId > 0 && $fileId > 0, "{$imageId}/{$fileId}");
$headers = function (string $path) use ($base): string {
    global $tmp;
    $ch = curl_init($base . $path);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_NOBODY => true, CURLOPT_COOKIEFILE => "{$tmp}/nimesh.jar"]);
    $out = (string) curl_exec($ch);
    curl_close($ch);
    return $out;
};
$imageHead = $headers("/attachments/{$imageId}");
check('images show inline with a fixed type', stripos($imageHead, 'Content-Type: image/png') !== false && stripos($imageHead, 'inline') !== false);
check('browser may not guess the type', stripos($imageHead, 'nosniff') !== false);
check('nothing can run inside a shared file', stripos($imageHead, 'sandbox') !== false);
$fileHead = $headers("/attachments/{$fileId}");
check('files always download', stripos($fileHead, 'Content-Disposition: attachment') !== false && stripos($fileHead, 'application/octet-stream') !== false);
$nimesh->get("/attachments/{$imageId}");
check('image bytes are intact', $nimesh->body === $png);
$new->get("/attachments/{$imageId}");
check('same faculty non-members cannot open group files', $new->status === 404, (string) $new->status);
$biz->get("/attachments/{$imageId}");
check('other faculties cannot open group files', $biz->status === 404, (string) $biz->status);
$guest->get("/attachments/{$imageId}");
check('guests cannot open files', $guest->status === 302);
$guest->get('/storage/uploads/attachments/x.png');
check('attachment folder is not public', $guest->status === 404);
$share($nimesh, '/groups/1/posts', '/groups/1', [], ['body' => '']);
check('an empty post is refused', str_contains($nimesh->follow()->body, 'add an image, file or GIF'));
$share($nimesh, '/groups/1/posts', '/groups/1', [[$tmp . '/ok.png', 'only-image.png']], ['body' => '']);
check('a post can be only an image', str_contains($nimesh->follow()->body, 'only-image.png'));

$share($nimesh, '/forum', '/forum/create', [[$tmp . '/ok.png', 'question-diagram.png']], ['title' => 'A question with a diagram attached', 'subject_id' => '1', 'body' => 'See the diagram 🤔']);
preg_match('#/forum/(\d+)$#', $nimesh->location, $m);
$forumQ = (int) ($m[1] ?? 0);
check('questions can have images', $forumQ > 0, $nimesh->location);
$other->get("/forum/{$forumQ}");
check('same faculty sees forum images', str_contains($other->body, 'question-diagram.png') && str_contains($other->body, '🤔'));
preg_match('#/attachments/(\d+)#', $other->body, $m);
$forumFile = (int) ($m[1] ?? 0);
$biz->get("/attachments/{$forumFile}");
check('other faculties cannot open forum images', $biz->status === 404, (string) $biz->status);
$share($other, "/forum/{$forumQ}/answers", "/forum/{$forumQ}", [[$tmp . '/ok.txt', 'answer.txt']], ['body' => '']);
$other->get("/forum/{$forumQ}");
check('answers can be only a file', str_contains($other->body, 'answer.txt'));
preg_match('#/answers/(\d+)/delete#', $other->body, $m);
$myAnswer = (int) ($m[1] ?? 0);
preg_match('#/attachments/(\d+)"[^>]*download#', $other->body, $m);
$answerFile = (int) ($m[1] ?? 0);
$third->post("/answers/{$myAnswer}/delete", [], '/dashboard');
check('others cannot delete an answer', $third->status === 403, (string) $third->status);
$other->post("/answers/{$myAnswer}/delete", [], "/forum/{$forumQ}");
$other->get("/attachments/{$answerFile}");
check('deleting an answer removes its file', $other->status === 404, (string) $other->status);
$nimesh->post("/forum/{$forumQ}/delete", [], "/forum/{$forumQ}");
$nimesh->get("/attachments/{$forumFile}");
check('deleting a question removes its files', $nimesh->status === 404, (string) $nimesh->status);

$nimesh->get('/groups/1');
preg_match('#/groups/posts/(\d+)/delete#', $nimesh->body, $m);
$postId = (int) ($m[1] ?? 0);
$other->get('/groups/1');
$other->post('/flag', ['target_type' => 'group_post', 'target_id' => $postId, 'reason' => 'Reporting a shared post'], '/groups/1');
$admin->get('/admin/moderation');
check('group posts can be reported', str_contains($admin->body, 'Reporting a shared post'));
$biz->post('/flag', ['target_type' => 'group_post', 'target_id' => $postId, 'reason' => 'Another faculty report'], '/dashboard');
check('other faculties cannot report group posts', $biz->status === 404, (string) $biz->status);
$biz->post("/groups/posts/{$postId}/delete", [], '/dashboard');
check('other faculties cannot delete group posts', $biz->status === 404, (string) $biz->status);
$dinithi = new Client('dinithi');
$dinithi->post('/login', ['login' => 'dinithi@students.nsbm.ac.lk', 'password' => 'Student@123']);
$dinithi->post("/groups/posts/{$postId}/delete", [], '/groups/1');
check('ordinary members cannot delete others posts', $dinithi->status === 403, (string) $dinithi->status);
$tharushi->post("/groups/posts/{$postId}/delete", [], '/groups/1');
check('the group owner can delete posts', str_contains($tharushi->location, '/groups/1'), $tharushi->location);

echo "GIFs\n";
$other->request('GET', '/api/gifs?q=study', null, ['Accept: application/json']);
if ($other->status === 503) {
    echo "  (skipped: start the server with GIPHY_API_KEY and tests/giphy-mock.php to test GIFs)\n";
} else {
    $found = json_decode($other->body, true);
    check('GIF search works', $other->status === 200 && count($found['data']['gifs']) > 0, $other->body);
    check('API key never reaches the browser', !str_contains($other->body, 'api_key') && !str_contains($other->body, 'test-secret-key'));
    $other->post('/groups/1/posts', ['body' => '', 'gif_id' => 'SafeGif123'], '/groups/1');
    $other->follow();
    check('a GIF can be posted', str_contains($other->body, 'media.giphy.com/media/SafeGif123/giphy.gif') && str_contains($other->body, 'via GIPHY'));
    check('API key is not on the page', !str_contains($other->body, 'test-secret-key'));
    foreach (['unknown' => 'NoSuchGif', 'adult-rated' => 'RatedRGif', 'injected' => 'x"><script>alert(1)</script>'] as $label => $badGif) {
        $other->post('/groups/1/posts', ['body' => 'with gif', 'gif_id' => $badGif], '/groups/1');
        check("{$label} GIF is refused", str_contains($other->follow()->body, "GIF isn") && !str_contains($other->body, '<script>alert(1)'));
    }
    for ($i = 0; $i < 31; $i++) {
        $other->request('GET', '/api/gifs?q=n' . $i, null, ['Accept: application/json']);
    }
    check('GIF search is rate limited', $other->status === 429, (string) $other->status);
}

echo "Theme\n";
$guest->get('/');
check('theme is set before first paint', strpos($guest->body, 'sc-theme') < strpos($guest->body, 'theme.css') && str_contains($guest->body, 'data-theme-toggle'));
foreach (['/login', '/register'] as $p) {
    $guest->get($p);
    check("{$p} has the theme switch", str_contains($guest->body, 'data-theme-toggle'));
}
$nimesh->get('/dashboard');
check('signed-in pages have the theme switch', str_contains($nimesh->body, 'data-theme-toggle'));

echo "Live numbers\n";
$json = fn (Client $c, string $p) => json_decode($c->request('GET', $p, null, ['Accept: application/json'])->body, true);
$public = $json($guest, '/api/stats/landing');
check('landing stats are public JSON', $guest->status === 200 && ($public['success'] ?? false));
check('landing stats contain no emails or names', !str_contains($guest->body, '@') && !str_contains($guest->body, 'Perera'));
check('landing stats are real', ($public['data']['values']['notes'] ?? 0) > 0 && count($public['data']['bars']['monthly']['items']) === 10 && count($public['data']['heat']['cells']) === 28);
$guest->get('/');
check('landing page uses the live numbers', str_contains($guest->body, 'data-live-url') && !str_contains($guest->body, '2,481'));
$guest->request('GET', '/api/stats/dashboard', null, ['Accept: application/json']);
check('dashboard stats need a sign-in', $guest->status === 401, (string) $guest->status);
$bizStats = $json($biz, '/api/stats/dashboard');
$compStats = $json($nimesh, '/api/stats/dashboard');
check('dashboard numbers differ per faculty', $bizStats['data']['values']['notes'] < $compStats['data']['values']['notes'], $bizStats['data']['values']['notes'] . ' vs ' . $compStats['data']['values']['notes']);
$before = $compStats['data']['values']['notes'];
$bizBefore = $bizStats['data']['values']['notes'];
$tok = $nimesh->token('/resources/create');
$nimesh->request('POST', '/resources', ['title' => 'Live counter note', 'subject_id' => '1', 'category_id' => '2', 'description' => 'Checks the live counter.', '_token' => $tok, 'file' => new CURLFile("{$tmp}/real.pdf", 'application/pdf', 'real.pdf')], ['Referer: ' . $base . '/resources/create']);
preg_match('#/resources/(\d+)$#', $nimesh->location, $m);
$admin->post('/admin/approvals/' . (int) ($m[1] ?? 0) . '/approve', [], '/admin/approvals');
$compStats = $json($nimesh, '/api/stats/dashboard');
$bizStats = $json($biz, '/api/stats/dashboard');
check('new note updates that faculty count', $compStats['data']['values']['notes'] === $before + 1, (string) $compStats['data']['values']['notes']);
check('new note leaves other faculties alone', $bizStats['data']['values']['notes'] === $bizBefore);

foreach (['week' => 7, 'month' => 4, 'half' => 6, 'nonsense' => 7] as $range => $bars) {
    $ranged = $json($nimesh, "/api/stats/dashboard?range={$range}");
    check("chart range {$range} has {$bars} bars", count($ranged['data']['bars']['weekly']['items']) === $bars);
}
$nimesh->get('/dashboard');
check('mastery pills link to their subject', str_contains($nimesh->body, 'class="dash-mastery__pill" href='));

echo "Admin faculty control\n";
$admin->post('/admin/users/6/faculty', ['faculty' => 'Nonsense'], '/admin/users');
check('admin faculty change validates', str_contains($admin->follow()->body, 'Choose a valid faculty'));
$biz->post('/admin/users/6/faculty', ['faculty' => 'Computing'], '/dashboard');
check('students cannot change faculty through admin', $biz->status === 403, (string) $biz->status);
$admin->post('/admin/users/6/faculty', ['faculty' => 'Computing'], '/admin/users');
$biz->get('/resources/1');
check('moving faculty changes access at once', $biz->status === 200, (string) $biz->status);

echo "Study space and settings\n";
$new->post('/space/log', ['minutes' => '90', 'logged_on' => date('Y-m-d'), 'note' => 'Smoke study'], '/space');
$new->get('/space');
check('study log is shown', str_contains($new->body, 'Smoke study') && str_contains($new->body, '1h 30m'));
$new->post('/space/log', ['minutes' => '90', 'logged_on' => date('Y-m-d', time() + 172800)], '/space');
check('future study log is refused', str_contains($new->follow()->body, 'cannot be in the future'));
$new->post('/space/goal', ['goal_hours' => '12'], '/space');
$new->get('/space');
check('goal updates', str_contains($new->body, 'of 12h this week'));
$new->post('/profile/edit', ['name' => 'Smoke Renamed', 'faculty' => 'Business', 'academic_year' => '3', 'bio' => $xss, 'skills' => "Smoke Skill, {$xss}", 'open_to_help' => '1', 'listed_in_directory' => '1'], '/profile/edit');
$new->get('/profile');
$new->follow();
check('profile saves and escapes', str_contains($new->body, 'Smoke Renamed') && !str_contains($new->body, $xss));
check('students cannot change their own faculty', str_contains($new->body, 'Faculty of Computing') && !str_contains($new->body, 'Faculty of Business'));
$new->get('/members/directory?q=' . urlencode('Smoke Skill'));
check('directory finds members by skill', str_contains($new->body, 'sc-member__who'));
check('directory never shows an email', !str_contains($new->body, '@students.nsbm.ac.lk'));
$new->post('/profile/edit', ['name' => 'Smoke Renamed', 'academic_year' => '3', 'skills' => 'Smoke Skill'], '/profile/edit');
$new->get('/members/directory?q=' . urlencode('Smoke Skill'));
check('members can hide from the directory', !str_contains($new->body, 'sc-member__who'));
$new->post('/settings/password', ['current_password' => 'Nope@1234', 'password' => 'Fresh@1234', 'password_confirmation' => 'Fresh@1234'], '/profile/edit');
check('wrong current password is refused', str_contains($new->follow()->body, 'not your current password'));
$new->post('/settings/password', ['current_password' => $password, 'password' => 'Fresh@1234', 'password_confirmation' => 'Fresh@1234'], '/profile/edit');
check('password changes', str_contains($new->follow()->body, 'Password changed'));

echo "Sign out, reset, remember me, throttle\n";
$new->post('/logout', [], '/dashboard');
$new->get('/dashboard');
check('sign out works', $new->status === 302);
$new->post('/login', ['login' => $email, 'password' => $password]);
check('old password no longer works', str_contains($new->follow()->body, 'Incorrect email or password'));
$new->post('/forgot-password', ['email' => 'nobody@students.nsbm.ac.lk'], '/forgot-password');
check('forgot password does not reveal accounts', str_contains($new->follow()->body, 'If that email is registered'));
$new->post('/forgot-password', ['email' => $email], '/forgot-password');
$resetLink = lastMailLink('/reset-password?token=');
clean($new, $resetLink);
preg_match('/name="token" value="([^"]+)"/', $new->body, $m);
$new->post('/reset-password', ['token' => $m[1], 'password' => 'Reset@1234', 'password_confirmation' => 'Reset@1234'], '/forgot-password');
check('password reset works', str_contains($new->follow()->body, 'Password updated'));
$new->post('/reset-password', ['token' => $m[1], 'password' => 'Again@1234', 'password_confirmation' => 'Again@1234'], '/forgot-password');
check('reset link is single use', str_contains($new->follow()->body, 'invalid or has expired'));
$new->post('/login', ['login' => $email, 'password' => 'Reset@1234', 'remember' => '1']);
check('new password signs in', str_contains($new->location, '/dashboard'), $new->location);
$jar = (string) file_get_contents("{$tmp}/new.jar");
file_put_contents("{$tmp}/new.jar", implode("\n", array_filter(explode("\n", $jar), fn ($l) => !str_contains($l, 'ncircle_session'))));
$new->get('/dashboard');
check('remember me restores a session', $new->status === 200, (string) $new->status);

$throttled = new Client('throttled');
for ($i = 0; $i < 5; $i++) {
    $throttled->post('/login', ['login' => 'nimasha@students.nsbm.ac.lk', 'password' => 'Wrong@1234']);
}
$throttled->post('/login', ['login' => 'nimasha@students.nsbm.ac.lk', 'password' => 'Student@123']);
check('repeated failures are throttled', str_contains($throttled->follow()->body, 'Too many attempts'));

echo "Blocking\n";
$admin->get('/admin/users?q=ravindu');
preg_match('#/admin/users/(\d+)/status#', $admin->body, $m);
$blockId = (int) $m[1];
$admin->post("/admin/users/{$blockId}/status", [], '/admin/users');
$third->get('/dashboard');
check('a blocked student is signed out at once', $third->status === 302);
$third->post('/login', ['login' => 'ravindu@students.nsbm.ac.lk', 'password' => 'Student@123']);
check('a blocked student cannot sign in', str_contains($third->follow()->body, 'has been blocked'));
$admin->post('/admin/users/1/status', [], '/admin/users');
check('admin cannot block themselves', str_contains($admin->follow()->body, 'cannot block your own'));

echo "Direct file access\n";
$guest->get('/../storage/uploads/resources/x');
check('storage is not served', $guest->status === 404);

array_map('unlink', glob("{$tmp}/*"));
rmdir($tmp);
echo "\n{$pass} passed, " . count($fail) . " failed\n";
foreach ($fail as $f) {
    echo " - {$f}\n";
}
exit($fail ? 1 : 0);
