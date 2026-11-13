<?php
/**
 * components/note-card.php  -  full note card from Browse notes
 *   <?= component('note-card', ['resource' => $r]) ?>
 * Needs the columns Resource::search() returns.
 */
$resource = $resource ?? [];
$id = (int) $resource["id"];
$fileType = strtoupper((string) $resource["file_type"]);
$size = format_bytes((int) $resource["file_size"]);
$tones = ["study-guide" => "guide", "past-paper" => "paper", "lecture-notes" => "lecture"];
$tone = $tones[$resource["category_slug"] ?? ""] ?? "guide";
$saved = !empty($resource["is_bookmarked"]);
?>
<article class="sc-note-card">
    <header class="sc-note-card__head">
        <?= component("avatar", ["name" => $resource["uploader"], "tone" => "teal"]) ?>
        <p class="sc-note-card__who">
            <a href="<?= e(url("/profile/" . (int) $resource["user_id"])) ?>"><?= e($resource["uploader"]) ?></a>
            <span>@<?= e($resource["uploader_username"]) ?> &middot; <?= e(time_ago($resource["created_at"])) ?></span>
        </p>
        <span class="sc-kind sc-kind--<?= e($tone) ?>"><?= e($resource["category"]) ?></span>
    </header>

    <div class="sc-note-card__body">
        <div class="sc-note-thumb" aria-hidden="true">
            <div class="sc-note-thumb__top"><span><?= e($resource["subject"]) ?></span><strong><?= e($fileType) ?></strong></div>
            <span class="sc-note-thumb__bar sc-note-thumb__bar--muted"></span>
            <span class="sc-note-thumb__bar sc-note-thumb__bar--lime"></span>
            <small><?= e($fileType) ?> &middot; <?= e($size) ?></small>
        </div>
        <div class="sc-note-card__main">
            <h3 class="sc-note-card__title"><a href="<?= e(url("/resources/" . $id)) ?>"><?= e($resource["title"]) ?></a></h3>
            <p class="sc-note-card__desc"><?= e($resource["description"]) ?></p>
            <div class="sc-note-card__tags">
                <a class="sc-hash" href="<?= e(url("/resources?subject[]=" . (int) $resource["subject_id"])) ?>">#<?= e($resource["subject"]) ?> <?= e($resource["subject_name"]) ?></a>
                <span class="sc-kind sc-kind--<?= e($tone) ?> sc-kind--square"><?= e($resource["category"]) ?></span>
                <span class="sc-note-card__file">File: <?= e($fileType) ?> &middot; <?= e($size) ?></span>
            </div>
        </div>
    </div>

    <footer class="sc-note-card__foot">
        <div class="sc-note-card__stats">
            <span class="sc-note-card__star"><i class="bi bi-star-fill" aria-hidden="true"></i> <strong><?= e(number_format((float) $resource["rating"], 1)) ?></strong> (<?= (int) $resource["rating_count"] ?>)</span>
            <span><i class="bi bi-download" aria-hidden="true"></i> <?= e(compact_number((int) $resource["downloads_count"])) ?> downloads</span>
            <a href="<?= e(url("/resources/" . $id . "#comments")) ?>"><i class="bi bi-chat-square" aria-hidden="true"></i> <?= (int) $resource["comments_count"] ?><span class="visually-hidden"> comments</span></a>
        </div>
        <div class="sc-note-card__actions">
            <form method="post" action="<?= e(url("/resources/" . $id . "/bookmark")) ?>">
                <?= csrf_field() ?>
                <button class="sc-round-btn<?= $saved ? " is-on" : "" ?>" type="submit" aria-pressed="<?= $saved ? "true" : "false" ?>" aria-label="<?= $saved ? "Remove from saved notes" : "Save note" ?>"><i class="bi bi-bookmark<?= $saved ? "-fill" : "" ?>" aria-hidden="true"></i></button>
            </form>
            <a class="btn btn-soft btn-xs" href="<?= e(url("/resources/" . $id . "/download")) ?>"><i class="bi bi-download" aria-hidden="true"></i> Download</a>
        </div>
    </footer>
</article>
