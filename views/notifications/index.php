<?php /** views/notifications/index.php */
$hasUnread = (bool) array_filter($result["rows"], fn ($n) => $n["read_at"] === null);
$readAll = $hasUnread ? '<form method="post" action="' . e(url("/notifications/read-all")) . '">' . csrf_field()
    . component("button", ["label" => "Mark all as read", "icon" => "check2-all", "variant" => "outline", "size" => "sm", "type" => "submit", "class" => "sc-btn-quiet"]) . '</form>' : "";
?>
<?= component("page-header", ["title" => "Notifications", "subtitle" => "Approvals, answers and activity on your work.", "actions" => $readAll]) ?>

<?php if ($result["rows"]): ?>
    <div class="sc-panel sc-panel--flush">
        <ul class="notif-list">
            <?php foreach ($result["rows"] as $n): ?>
                <li class="notif<?= $n["read_at"] === null ? " notif--unread" : "" ?>">
                    <span class="sc-icon-circle"><i class="bi bi-<?= e($n["icon"]) ?>" aria-hidden="true"></i></span>
                    <div class="notif__body">
                        <a class="stretched-link" href="<?= e(url("/notifications/" . (int) $n["id"] . "/open")) ?>"><?= e($n["title"]) ?></a>
                        <?php if ($n["body"]): ?><p><?= e($n["body"]) ?></p><?php endif; ?>
                    </div>
                    <time><?= e(time_ago($n["created_at"])) ?></time>
                    <?php if ($n["read_at"] === null): ?><span class="sc-dot sc-dot--sm"><span class="visually-hidden">Unread</span></span><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="mt-4"><?= component("pagination", ["page" => $page, "pages" => $result["pages"], "baseUrl" => url("/notifications")]) ?></div>
<?php else: ?>
    <div class="sc-card"><?= component("empty-state", ["icon" => "bell", "title" => "You're all caught up", "text" => "New approvals and answers will show up here."]) ?></div>
<?php endif; ?>
