<?php
/** views/groups/show.php  -  one study group: wall, members, sessions */
$id = (int) $group["id"];
$isFull = (int) $group["members_count"] >= (int) $group["max_members"];
?>
<nav class="sc-crumb" aria-label="Breadcrumb"><a href="<?= e(url("/groups")) ?>"><i class="bi bi-arrow-left" aria-hidden="true"></i> Study groups</a></nav>

<header class="sc-panel group-hero">
    <span class="sc-group__icon sc-group__icon--lg"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
    <div class="group-hero__text">
        <h1 class="thread-title m-0"><?= e($group["name"]) ?></h1>
        <p class="sc-group__subject">#<?= e($group["subject"]) ?> <?= e($group["subject_name"]) ?> &middot; <?= (int) $group["members_count"] ?>/<?= (int) $group["max_members"] ?> members &middot; run by <?= e($group["owner"]) ?></p>
        <p class="sc-post__text"><?= e($group["description"]) ?></p>
    </div>
    <div class="group-hero__actions">
        <?php if ($isOwner || $isAdmin): ?>
            <?= component("button", ["label" => "Delete group", "icon" => "trash", "variant" => "outline", "size" => "sm", "class" => "sc-btn-quiet sc-btn-danger-text", "attrs" => ["data-bs-toggle" => "modal", "data-bs-target" => "#deleteGroup"]]) ?>
        <?php endif; ?>
        <?php if ($isMember && !$isOwner): ?>
            <form method="post" action="<?= e(url("/groups/" . $id . "/leave")) ?>">
                <?= csrf_field() ?>
                <?= component("button", ["label" => "Leave group", "variant" => "outline", "size" => "sm", "type" => "submit", "class" => "sc-btn-quiet"]) ?>
            </form>
        <?php elseif (!$isMember): ?>
            <form method="post" action="<?= e(url("/groups/" . $id . "/join")) ?>">
                <?= csrf_field() ?>
                <?= component("button", ["label" => $isFull ? "Group is full" : "Join group", "variant" => "dark", "size" => "sm", "type" => "submit", "attrs" => ["disabled" => $isFull]]) ?>
            </form>
        <?php endif; ?>
    </div>
</header>

<div class="note-detail">
    <div>
        <section class="sc-panel" id="wall" aria-labelledby="wall-title">
            <h2 class="sc-panel__title" id="wall-title"><i class="bi bi-chat-left-text" aria-hidden="true"></i> Group wall</h2>
            <?php if ($isMember): ?>
                <form method="post" action="<?= e(url("/groups/" . $id . "/posts")) ?>" class="mt-3" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?= component("composer", ["name" => "body", "label" => "Share something with the group", "rows" => 2, "maxlength" => 1000]) ?>
                    <?= component("button", ["label" => "Post", "type" => "submit", "variant" => "dark", "size" => "sm"]) ?>
                </form>
                <?php foreach ($posts as $post): ?>
                    <div class="sc-post">
                        <?= component("avatar", ["name" => $post["author"], "size" => "sm"]) ?>
                        <div class="sc-post__body">
                            <div class="sc-post__meta">
                                <strong><?= e($post["author"]) ?></strong> <span><?= e(time_ago($post["created_at"])) ?></span>
                                <span class="sc-post__tools">
                                    <?php if ((int) $post["user_id"] === Auth::id() || $isOwner || $isAdmin): ?>
                                        <form method="post" action="<?= e(url("/groups/posts/" . (int) $post["id"] . "/delete")) ?>" data-confirm="Delete this post?">
                                            <?= csrf_field() ?>
                                            <button class="sc-text-btn sc-text-btn--danger" type="submit" aria-label="Delete this post"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ((int) $post["user_id"] !== Auth::id()): ?>
                                        <button class="sc-text-btn" type="button" data-bs-toggle="modal" data-bs-target="#flagModal" data-flag-type="group_post" data-flag-id="<?= (int) $post["id"] ?>" aria-label="Report this post"><i class="bi bi-flag" aria-hidden="true"></i></button>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if ($post["body"] !== ""): ?><p class="sc-post__text"><?= nl2br(e($post["body"])) ?></p><?php endif; ?>
                            <?= component("attachment-list", ["attachments" => $post["attachments"]]) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$posts): ?><p class="text-muted-sc mt-3">Nothing here yet. Say hello.</p><?php endif; ?>
            <?php else: ?>
                <p class="text-muted-sc mt-3">Join this group to read and post on the wall.</p>
            <?php endif; ?>
        </section>

        <section class="sc-panel" id="meetings" aria-labelledby="group-meetings">
            <header class="sc-panel__head">
                <h2 class="sc-panel__title" id="group-meetings"><span class="sc-dot sc-dot--sm" aria-hidden="true"></span> Upcoming meetings</h2>
                <?php if ($isMember): ?><?= component("button", ["label" => "Schedule meeting", "icon" => "plus-circle", "variant" => "outline", "size" => "sm", "href" => url("/sessions/create?group=" . $id), "class" => "sc-btn-quiet"]) ?><?php endif; ?>
            </header>
            <?php if ($isMember || $isAdmin): ?>
                <?php if ($upcoming): ?>
                    <div class="sc-card-grid sc-card-grid--two"><?php foreach ($upcoming as $session): ?><?= component("pod-card", ["session" => $session]) ?><?php endforeach; ?></div>
                <?php else: ?>
                    <p class="text-muted-sc">No meetings scheduled yet.</p>
                <?php endif; ?>
                <?php if ($past): ?>
                    <h3 class="sc-panel__title group-past-title">Past meetings</h3>
                    <div class="sc-card-grid sc-card-grid--two"><?php foreach ($past as $session): ?><?= component("pod-card", ["session" => $session]) ?><?php endforeach; ?></div>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted-sc">Join this group to see its meetings.</p>
            <?php endif; ?>
        </section>
    </div>
    <aside>
        <section class="sc-panel sc-panel--tight" aria-labelledby="members-title">
            <h2 class="sc-panel__title" id="members-title">Members</h2>
            <ul class="sc-member-list">
                <?php foreach ($members as $member): ?>
                    <li>
                        <?= component("avatar", ["name" => $member["name"], "size" => "sm"]) ?>
                        <a href="<?= e(url("/profile/" . (int) $member["id"])) ?>"><?= e($member["name"]) ?></a>
                        <?php if ((int) $member["id"] === (int) $group["owner_id"]): ?><span class="sc-state sc-state--muted">OWNER</span><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </aside>
</div>

<?= component("flag-modal") ?>
<?php if ($isOwner || $isAdmin): ?>
    <?= component("confirm-modal", ["id" => "deleteGroup", "title" => "Delete this group?", "message" => "The wall and member list are removed. This cannot be undone.", "action" => url("/groups/" . $id . "/delete"), "confirmLabel" => "Delete"]) ?>
<?php endif; ?>
