<?php /** views/profile/show.php  -  a student's public profile */ ?>
<header class="sc-panel group-hero">
    <?= component("avatar", ["name" => $profile["name"], "photo" => avatar_url($profile["id"], $profile["avatar"]), "tone" => "teal", "size" => "lg"]) ?>
    <div class="group-hero__text">
        <h1 class="thread-title m-0"><?= e($profile["name"]) ?></h1>
        <p class="sc-group__subject">@<?= e($profile["username"]) ?> &middot; <?= e(config("app.faculties")[$profile["faculty"]] ?? $profile["faculty"]) ?> &middot; Year <?= (int) $profile["academic_year"] ?></p>
        <?php if ($profile["bio"]): ?><p class="sc-post__text"><?= nl2br(e($profile["bio"])) ?></p><?php endif; ?>
        <?= component("skill-list", ["skills" => $profile["skills"], "open" => (bool) $profile["open_to_help"]]) ?>
    </div>
    <?php if ($isMe): ?>
        <div class="group-hero__actions"><?= component("button", ["label" => "Edit profile", "icon" => "pencil", "variant" => "outline", "size" => "sm", "href" => url("/profile/edit"), "class" => "sc-btn-quiet"]) ?></div>
    <?php endif; ?>
</header>

<section class="dash-stats" aria-label="Contributions">
    <?= component("stat-tile", ["label" => "Notes shared", "value" => count($notes), "footIcon" => "folder2", "foot" => "Approved uploads"]) ?>
    <?= component("stat-tile", ["label" => "Answers", "value" => $answers, "footIcon" => "patch-check", "foot" => $accepted . " accepted"]) ?>
    <?= component("stat-tile", ["label" => "Study groups", "value" => $groups, "footIcon" => "people", "foot" => "Member of"]) ?>
</section>

<section aria-labelledby="profile-notes">
    <h2 class="sc-panel__title answers-title" id="profile-notes">Shared notes</h2>
    <?php if ($notes): ?>
        <div class="browse-list__items">
            <?php foreach ($notes as $note): ?><?= component("note-card", ["resource" => $note]) ?><?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="sc-card"><?= component("empty-state", ["icon" => "folder2-open", "title" => "No notes shared yet"]) ?></div>
    <?php endif; ?>
</section>
