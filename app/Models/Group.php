<?php
/** app/Models/Group.php  -  study groups, members and the group wall */
class Group
{
    private const SELECT = 'SELECT g.*, s.code AS subject, s.name AS subject_name, s.faculty AS subject_faculty, u.name AS owner,
            (SELECT COUNT(*) FROM group_members m WHERE m.group_id = g.id) AS members_count
         FROM study_groups g JOIN subjects s ON s.id = g.subject_id JOIN users u ON u.id = g.owner_id';

    public static function find(int $id): ?array
    {
        return Database::one(self::SELECT . ' WHERE g.id = ?', [$id]);
    }

    public static function search(array $f, int $userId, int $page, int $perPage = 9): array
    {
        $where = ['1=1'];
        $params = [];
        if (($f['q'] ?? '') !== '') {
            $where[] = '(g.name LIKE ? OR g.description LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like);
        }
        if (($f['faculty'] ?? null) !== null) {
            $where[] = 's.faculty = ?';
            $params[] = $f['faculty'];
        }
        if (!empty($f['subject'])) {
            $where[] = 'g.subject_id = ?';
            $params[] = (int) $f['subject'];
        }
        if (!empty($f['mine'])) {
            $where[] = 'EXISTS (SELECT 1 FROM group_members m WHERE m.group_id = g.id AND m.user_id = ?)';
            $params[] = $userId;
        }
        $sql = implode(' AND ', $where);
        $total = (int) Database::value("SELECT COUNT(*) FROM study_groups g JOIN subjects s ON s.id = g.subject_id WHERE {$sql}", $params);
        $offset = ($page - 1) * $perPage;
        $rows = Database::all(self::SELECT . " WHERE {$sql} ORDER BY g.created_at DESC LIMIT {$perPage} OFFSET {$offset}", $params);
        return ['rows' => self::attachPreview($rows, $userId), 'total' => $total, 'pages' => (int) ceil($total / $perPage)];
    }

    /** Adds is_member and up to three members for the avatar stack. */
    public static function attachPreview(array $groups, int $userId): array
    {
        foreach ($groups as &$g) {
            $g['is_member'] = self::isMember((int) $g['id'], $userId);
            $g['preview'] = Database::all(
                'SELECT u.id, u.name, u.avatar FROM group_members m JOIN users u ON u.id = m.user_id WHERE m.group_id = ? ORDER BY m.joined_at LIMIT 3',
                [$g['id']]
            );
        }
        return $groups;
    }

    public static function forUser(int $userId, int $limit = 4): array
    {
        $rows = Database::all(
            self::SELECT . ' JOIN group_members me ON me.group_id = g.id AND me.user_id = ? ORDER BY me.joined_at DESC LIMIT ' . (int) $limit,
            [$userId]
        );
        return self::attachPreview($rows, $userId);
    }

    public static function create(int $ownerId, array $d): int
    {
        return Database::transaction(function () use ($ownerId, $d) {
            $id = Database::insert(
                'INSERT INTO study_groups (owner_id, subject_id, name, description, max_members) VALUES (?, ?, ?, ?, ?)',
                [$ownerId, (int) $d['subject_id'], $d['name'], $d['description'], (int) $d['max_members']]
            );
            Database::run('INSERT INTO group_members (group_id, user_id) VALUES (?, ?)', [$id, $ownerId]);
            return $id;
        });
    }

    /** Locks the row like join() does. Returns false when the new limit is below the current members. */
    public static function update(int $id, array $d): bool
    {
        return Database::transaction(function () use ($id, $d) {
            Database::value('SELECT id FROM study_groups WHERE id = ? FOR UPDATE', [$id]);
            $count = (int) Database::value('SELECT COUNT(*) FROM group_members WHERE group_id = ?', [$id]);
            if ((int) $d['max_members'] < $count) {
                return false;
            }
            Database::run(
                'UPDATE study_groups SET name = ?, description = ?, max_members = ? WHERE id = ?',
                [$d['name'], $d['description'], (int) $d['max_members'], $id]
            );
            return true;
        });
    }

    public static function delete(int $id): void
    {
        $postIds = array_map('intval', array_column(Database::all('SELECT id FROM group_posts WHERE group_id = ?', [$id]), 'id'));
        Attachment::deleteFor('group_post', $postIds);
        Database::run('DELETE FROM study_groups WHERE id = ?', [$id]);
    }

    public static function isMember(int $groupId, int $userId): bool
    {
        return Database::value('SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?', [$groupId, $userId]) !== null;
    }

    public static function members(int $groupId): array
    {
        return Database::all(
            'SELECT u.id, u.name, u.username, u.avatar, u.faculty, m.joined_at FROM group_members m JOIN users u ON u.id = m.user_id
             WHERE m.group_id = ? ORDER BY m.joined_at',
            [$groupId]
        );
    }

    /** Locks the group row so two students cannot take the last seat. Returns false when full. */
    public static function join(int $groupId, int $userId): bool
    {
        return Database::transaction(function () use ($groupId, $userId) {
            $max = (int) Database::value('SELECT max_members FROM study_groups WHERE id = ? FOR UPDATE', [$groupId]);
            $count = (int) Database::value('SELECT COUNT(*) FROM group_members WHERE group_id = ?', [$groupId]);
            if ($count >= $max) {
                return false;
            }
            Database::run('INSERT IGNORE INTO group_members (group_id, user_id) VALUES (?, ?)', [$groupId, $userId]);
            return true;
        });
    }

    public static function leave(int $groupId, int $userId): void
    {
        Database::run('DELETE FROM group_members WHERE group_id = ? AND user_id = ?', [$groupId, $userId]);
    }

    public static function posts(int $groupId): array
    {
        return Database::all(
            'SELECT p.*, u.name AS author, u.avatar FROM group_posts p JOIN users u ON u.id = p.user_id
             WHERE p.group_id = ? AND p.is_hidden = 0 ORDER BY p.created_at DESC LIMIT 50',
            [$groupId]
        );
    }

    public static function addPost(int $groupId, int $userId, string $body): int
    {
        return Database::insert('INSERT INTO group_posts (group_id, user_id, body) VALUES (?, ?, ?)', [$groupId, $userId, $body]);
    }

    public static function findPost(int $id): ?array
    {
        return Database::one('SELECT * FROM group_posts WHERE id = ?', [$id]);
    }

    public static function deletePost(int $id): void
    {
        Attachment::deleteFor('group_post', [$id]);
        Database::run('DELETE FROM group_posts WHERE id = ?', [$id]);
    }
}
