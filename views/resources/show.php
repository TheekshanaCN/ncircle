<?php
/** views/resources/show.php  -  one note: details, rating, download, comments */
$id = (int) $resource["id"];
$fileType = strtoupper($resource["file_type"]);
$approved = $resource["status"] === "approved";
?>
<?php if (!$approved): ?>
    <?php if ($resource["status"] === "pending"): ?>
        <?= component("alert", ["type" => "warning", "message" => "This note is waiting for approval. Only you and admins can see it."]) ?>
    <?php else: ?>
        <?= component("alert", ["type" => "danger", "title" => "This note was not approved", "message" => $resource["reject_reason"] ?: "No reason was given."]) ?>
    <?php endif; ?>
<?php endif; ?>

<div class="note-detail">
    <div>
        <article class="sc-panel">
            <header class="note-detail__head">
                <span class="sc-note-row__type sc-note-row__type--lg"><?= e(substr($fileType, 0, 4)) ?></span>
                <div>
                    <h1 class="note-detail__title"><?= e($resource["title"]) ?></h1>
                    <p class="note-detail__by">by <a href="<?= e(url("/profile/" . (int) $resource["user_id"])) ?>"><?= e($resource["uploader"]) ?></a> &middot; <?= e(time_ago($resource["created_at"])) ?></p>
                </div>
            </header>
            <div class="note-detail__tags">
                <a class="sc-hash" href="<?= e(url("/resources?subject[]=" . (int) $resource["subject_id"])) ?>">#<?= e($resource["subject"]) ?> <?= e($resource["subject_name"]) ?></a>
                <span class="sc-chip"><?= e($resource["category"]) ?></span>
                <span class="sc-chip">Year <?= (int) $resource["subject_year"] ?></span>
                <?php if (!$approved): ?><?= component("status-badge", ["status" => $resource["status"]]) ?><?php endif; ?>
            </div>
            <p class="note-detail__desc"><?= nl2br(e($resource["description"])) ?></p>

            <dl class="note-detail__facts">
                <div><dt>File</dt><dd><?= e($fileType) ?> &middot; <?= e(format_bytes((int) $resource["file_size"])) ?></dd></div>
                <?php if ($resource["pages"]): ?><div><dt>Pages</dt><dd><?= (int) $resource["pages"] ?></dd></div><?php endif; ?>
                <div><dt>Downloads</dt><dd><?= number_format((int) $resource["downloads_count"]) ?></dd></div>
                <div><dt>Rating</dt><dd><i class="bi bi-star-fill note-detail__star" aria-hidden="true"></i> <span data-rating-summary><?= e(number_format((float) $resource["rating"], 1)) ?> (<?= (int) $resource["rating_count"] ?>)</span></dd></div>
            </dl>

            <div class="note-detail__actions">
                <?= component("button", ["label" => "Download", "icon" => "download", "href" => url("/resources/" . $id . "/download")]) ?>
                <?php if ($approved): ?>
                    <form method="post" action="<?= e(url("/resources/" . $id . "/bookmark")) ?>">
                        <?= csrf_field() ?>
                        <?= component("button", ["label" => $resource["is_bookmarked"] ? "Saved" : "Save", "icon" => $resource["is_bookmarked"] ? "bookmark-fill" : "bookmark", "variant" => "outline", "type" => "submit", "class" => "sc-btn-quiet"]) ?>
                    </form>
                <?php endif; ?>
                <?php if ($isOwner || $isAdmin): ?>
                    <?= component("button", ["label" => "Delete", "icon" => "trash", "variant" => "outline", "class" => "sc-btn-quiet sc-btn-danger-text", "attrs" => ["data-bs-toggle" => "modal", "data-bs-target" => "#deleteNote"]]) ?>
                <?php elseif ($approved): ?>
                    <button class="sc-text-btn" type="button" data-bs-toggle="modal" data-bs-target="#flagModal" data-flag-type="resource" data-flag-id="<?= $id ?>"><i class="bi bi-flag" aria-hidden="true"></i> Report</button>
                <?php endif; ?>
            </div>
        </article>

        <?php if ($approved): ?>
        <section class="sc-panel" id="comments" aria-labelledby="comments-title">
            <h2 class="sc-panel__title" id="comments-title"><i class="bi bi-chat-square" aria-hidden="true"></i> Comments (<?= count($comments) ?>)</h2>
            <?php foreach ($comments as $comment): ?>
                <div class="sc-post">
                    <?= component("avatar", ["name" => $comment["author"], "size" => "sm"]) ?>
                    <div class="sc-post__body">
                        <p class="sc-post__meta"><a href="<?= e(url("/profile/" . (int) $comment["user_id"])) ?>"><strong><?= e($comment["author"]) ?></strong></a> <span><?= e(time_ago($comment["created_at"])) ?></span>
                            <?php if ((int) $comment["user_id"] !== Auth::id()): ?><button class="sc-text-btn" type="button" data-bs-toggle="modal" data-bs-target="#flagModal" data-flag-type="comment" data-flag-id="<?= (int) $comment["id"] ?>" aria-label="Report this comment"><i class="bi bi-flag" aria-hidden="true"></i></button><?php endif; ?>
                        </p>
                        <p class="sc-post__text"><?= nl2br(e($comment["body"])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (!$comments): ?><p class="text-muted-sc">No comments yet. Ask a question or say thanks.</p><?php endif; ?>
            <form method="post" action="<?= e(url("/resources/" . $id . "/comments")) ?>" class="mt-3">
                <?= csrf_field() ?>
                <?= component("form-field", ["name" => "body", "label" => "Add a comment", "type" => "textarea", "rows" => 3, "required" => true, "attrs" => ["maxlength" => 1000]]) ?>
                <?= component("button", ["label" => "Post comment", "type" => "submit", "variant" => "dark", "size" => "sm"]) ?>
            </form>
        </section>
        <?php endif; ?>
    </div>

    <aside>
        <?php if ($approved && !$isOwner): ?>
            <section class="sc-panel sc-panel--tight">
                <h2 class="sc-panel__title">Rate this note</h2>
                <?= component("rating-stars", ["rating" => $myRating, "interactive" => true, "url" => url("/api/resources/" . $id . "/rate")]) ?>
                <p class="sc-panel__sub mt-2"><?= $myRating ? "You rated this. Tap to change it." : "Tap a star. You can change it later." ?></p>
            </section>
        <?php endif; ?>
        <?php if ($related): ?>
            <section class="sc-panel sc-panel--tight">
                <h2 class="sc-panel__title">More from <?= e($resource["subject"]) ?></h2>
                <div class="sc-stack">
                    <?php foreach ($related as $note): ?><?= component("note-row", ["resource" => $note]) ?><?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </aside>
</div>

<?= component("flag-modal") ?>
<?php if ($isOwner || $isAdmin): ?>
    <?= component("confirm-modal", ["id" => "deleteNote", "title" => "Delete this note?", "message" => "This cannot be undone.", "action" => url("/resources/" . $id . "/delete"), "confirmLabel" => "Delete"]) ?>
<?php endif; ?>
