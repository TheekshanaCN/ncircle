<?php /** views/admin/approvals.php  -  review uploaded notes */ ?>
<?= component("page-header", ["title" => "Note approvals", "subtitle" => "Check formatting, academic ethics and legibility before a note goes live."]) ?>

<?php if ($pending): ?>
    <div class="sc-stack">
        <?php foreach ($pending as $note): $id = (int) $note["id"]; ?>
            <article class="sc-panel sc-panel--tight approval">
                <span class="sc-note-row__type"><?= e(strtoupper(substr($note["file_type"], 0, 3))) ?></span>
                <div class="approval__main">
                    <h2 class="sc-note-row__title"><a href="<?= e(url("/resources/" . $id)) ?>"><?= e($note["title"]) ?></a></h2>
                    <p class="sc-note-row__by"><?= e($note["subject"]) ?> <?= e($note["subject_name"]) ?> &middot; <?= e($note["category"]) ?> &middot; by <?= e($note["uploader"]) ?> &middot; <?= e(time_ago($note["created_at"])) ?></p>
                    <p class="sc-post__text"><?= e($note["description"]) ?></p>
                </div>
                <div class="approval__actions">
                    <?= component("button", ["label" => "File", "icon" => "download", "variant" => "outline", "size" => "sm", "class" => "sc-btn-quiet", "href" => url("/resources/" . $id . "/download")]) ?>
                    <?= component("button", ["label" => "Reject", "variant" => "outline", "size" => "sm", "class" => "sc-btn-quiet sc-btn-danger-text", "attrs" => ["data-bs-toggle" => "collapse", "data-bs-target" => "#reject-" . $id, "aria-expanded" => "false"]]) ?>
                    <form method="post" action="<?= e(url("/admin/approvals/" . $id . "/approve")) ?>">
                        <?= csrf_field() ?>
                        <?= component("button", ["label" => "Approve", "icon" => "check-lg", "variant" => "dark", "size" => "sm", "type" => "submit"]) ?>
                    </form>
                </div>
                <form class="collapse approval__reject" id="reject-<?= $id ?>" method="post" action="<?= e(url("/admin/approvals/" . $id . "/reject")) ?>">
                    <?= csrf_field() ?>
                    <?= component("form-field", ["id" => "reason-" . $id, "name" => "reason", "label" => "Reason shown to the uploader", "required" => true, "value" => "", "error" => "", "attrs" => ["maxlength" => 255, "minlength" => 5]]) ?>
                    <?= component("button", ["label" => "Reject note", "variant" => "danger", "size" => "sm", "type" => "submit"]) ?>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="sc-card"><?= component("empty-state", ["icon" => "check2-circle", "title" => "Queue is clear", "text" => "New uploads will appear here."]) ?></div>
<?php endif; ?>
