<?php
/**
 * views/partials/sidebar.php  -  left menu. $sidebarMode comes from the layout.
 * Off-canvas below the lg breakpoint, opened from the top bar.
 */
$user = Auth::user();
$isAdminMode = ($sidebarMode ?? "student") === "admin";
$pendingCount = $isAdminMode ? (int) Database::value("SELECT COUNT(*) FROM resources WHERE status = 'pending'") : 0;
$openReports = $isAdminMode ? (int) Database::value("SELECT COUNT(*) FROM reports WHERE status = 'open'") : 0;

$sections = $isAdminMode
    ? [
        ["Admin", [
            ["/admin", "grid-1x2", "Admin overview", 0],
            ["/admin/approvals", "patch-check", "Note approvals", $pendingCount],
            ["/admin/moderation", "flag", "Moderation", $openReports],
        ]],
        ["Manage", [
            ["/admin/users", "people", "Users", 0],
            ["/admin/subjects", "journal-bookmark", "Subjects", 0],
            ["/admin/reports", "bar-chart", "Reports", 0],
        ]],
    ]
    : [
        ["Study", [
            ["/dashboard", "grid-1x2", "Dashboard overview", 0],
            ["/resources", "folder2-open", "Browse notes", 0],
            ["/forum", "chat-left-text", "Discussion forum", 0],
        ]],
        ["Community", [
            ["/members", "person-raised-hand", "Find Members", 0],
            ["/groups", "people", "Study groups", 0],
            ["/sessions", "calendar-event", "Meetings", 0],
        ]],
        ["Personal", [
            ["/space", "person-circle", "Personal study space", 0],
        ]],
    ];

$isActive = fn (string $href): bool => $href === "/admin" ? current_path() === "/admin" : is_path($href);
?>
<aside class="sc-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="scSidebar" aria-label="Main menu">
    <div class="sc-sidebar__inner">
        <div class="sc-sidebar__brand">
            <a class="sc-sidebar__logo" href="<?= e(url($isAdminMode ? "/admin" : "/dashboard")) ?>"><img src="<?= e(asset("img/logo.svg")) ?>" alt="Ncircle home"></a>
            <button type="button" class="sc-sidebar__toggle" data-sidebar-toggle aria-label="Collapse menu" aria-expanded="true"><i class="bi bi-layout-sidebar" aria-hidden="true"></i></button>
            <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#scSidebar" aria-label="Close menu"></button>
        </div>

        <?php if ($isAdminMode): ?>
            <?= component("button", ["label" => "Back to student view", "icon" => "arrow-left", "variant" => "dark", "href" => url("/dashboard"), "class" => "sc-sidebar__cta", "attrs" => ["data-sc-tip" => "Back to student view"]]) ?>
        <?php else: ?>
            <?= component("button", ["label" => "Share a note", "icon" => "plus-lg", "variant" => "dark", "href" => url("/resources/create"), "class" => "sc-sidebar__cta", "attrs" => ["data-sc-tip" => "Share a note"]]) ?>
        <?php endif; ?>

        <nav class="sc-sidebar__nav" aria-label="Sections">
            <?php foreach ($sections as [$heading, $links]): ?>
                <p class="sc-sidebar__heading"><?= e($heading) ?></p>
                <?php foreach ($links as [$href, $icon, $label, $count]): $active = $isActive($href); ?>
                    <a class="sc-sidebar__link<?= $active ? " is-active" : "" ?>" href="<?= e(url($href)) ?>" data-sc-tip="<?= e($label) ?>"<?= $active ? ' aria-current="page"' : "" ?>>
                        <i class="bi bi-<?= e($icon) ?>" aria-hidden="true"></i>
                        <span class="sc-sidebar__label"><?= e($label) ?></span>
                        <?php if ($count > 0): ?><span class="sc-sidebar__count"><?= (int) $count ?></span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php if (!$isAdminMode && ($user["role"] ?? "") === "admin"): ?>
                <a class="sc-sidebar__link" href="<?= e(url("/admin")) ?>" data-sc-tip="Admin panel"><i class="bi bi-shield-check" aria-hidden="true"></i><span class="sc-sidebar__label">Admin panel</span></a>
            <?php endif; ?>
        </nav>

        <div class="sc-sidebar__foot">
            <a class="sc-sidebar__link sc-sidebar__link--sm<?= is_path("/profile/edit") ? " is-active" : "" ?>" href="<?= e(url("/profile/edit")) ?>" data-sc-tip="Settings"><i class="bi bi-gear" aria-hidden="true"></i><span class="sc-sidebar__label">Settings</span></a>
            <a class="sc-sidebar__link sc-sidebar__link--sm<?= is_path("/help") ? " is-active" : "" ?>" href="<?= e(url("/help")) ?>" data-sc-tip="Help Center"><i class="bi bi-question-circle" aria-hidden="true"></i><span class="sc-sidebar__label">Help Center</span></a>

            <div class="sc-user-widget">
                <?= component("avatar", ["name" => $user["name"], "photo" => avatar_url($user["id"], $user["avatar"]), "tone" => "teal"]) ?>
                <a class="sc-user-widget__text" href="<?= e(url("/profile")) ?>">
                    <strong><?= e($user["name"]) ?></strong>
                    <small>@<?= e($user["student_id"]) ?> &middot; <?= e($user["faculty"]) ?></small>
                </a>
                <div class="dropup">
                    <button class="sc-user-widget__menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu"><i class="bi bi-three-dots-vertical" aria-hidden="true"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= e(url("/profile")) ?>"><i class="bi bi-person" aria-hidden="true"></i> My profile</a></li>
                        <li><a class="dropdown-item" href="<?= e(url("/profile/edit")) ?>"><i class="bi bi-gear" aria-hidden="true"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= e(url("/logout")) ?>">
                                <?= csrf_field() ?>
                                <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Sign out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</aside>
