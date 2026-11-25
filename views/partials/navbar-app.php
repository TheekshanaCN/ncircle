<?php
/** views/partials/navbar-app.php  -  floating top bar for signed-in pages */
$user = Auth::user();
$unread = Notification::unreadCount((int) $user["id"]);
?>
<header class="sc-topbar-wrap">
    <div class="sc-topbar">
        <button class="sc-round-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#scSidebar" aria-controls="scSidebar" aria-label="Open menu"><i class="bi bi-list" aria-hidden="true"></i></button>

        <form class="sc-topbar__search" action="<?= e(url("/search")) ?>" method="get" role="search">
            <i class="bi bi-search" aria-hidden="true"></i>
            <label class="visually-hidden" for="topSearch">Search Ncircle</label>
            <input id="topSearch" type="search" name="q" value="<?= e(is_path("/search") ? Request::query("q") : "") ?>" placeholder="Search notes, study pods, lecture slides, question threads...">
        </form>

        <div class="sc-topbar__actions">
            <?= component("theme-toggle") ?>
            <a class="sc-round-btn" href="<?= e(url("/notifications")) ?>" aria-label="Notifications<?= $unread ? " ({$unread} unread)" : "" ?>">
                <i class="bi bi-bell" aria-hidden="true"></i>
                <?php if ($unread): ?><span class="sc-round-btn__dot" aria-hidden="true"></span><?php endif; ?>
            </a>
            <a class="sc-round-btn" href="<?= e(url("/profile/edit")) ?>" aria-label="Settings"><i class="bi bi-sliders" aria-hidden="true"></i></a>
            <span class="sc-topbar__divider" aria-hidden="true"></span>
            <a class="sc-topbar__profile" href="<?= e(url("/profile")) ?>">
                <?= component("avatar", ["name" => $user["name"], "photo" => avatar_url($user["id"], $user["avatar"]), "tone" => "teal", "ring" => true]) ?>
                <span class="sc-topbar__who">
                    <strong><?= e($user["name"]) ?></strong>
                    <small><?= e($user["username"]) ?> | <?= e($user["student_id"]) ?></small>
                </span>
            </a>
        </div>
    </div>
</header>
