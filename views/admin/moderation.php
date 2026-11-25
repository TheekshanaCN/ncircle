<?php /** views/admin/moderation.php  -  reported content */ ?>
<?= component("page-header", ["title" => "Moderation", "subtitle" => "Content students reported. Remove it, or dismiss the report."]) ?>

<?php if ($reports): ?>
    <div class="sc-stack">
        <?php foreach ($reports as $report): $id = (int) $report["id"]; $target = $report["target"]; ?>
            <article class="sc-panel sc-panel--tight approval">
                <span class="sc-icon-circle"><i class="bi bi-flag" aria-hidden="true"></i></span>
                <div class="approval__main">
                    <h2 class="sc-note-row__title"><?= e(ucfirst(str_replace("_", " ", $report["target_type"]))) ?> reported by <?= e($report["reporter"]) ?></h2>
                    <p class="sc-note-row__by"><?= e(time_ago($report["created_at"])) ?></p>
                    <p class="sc-post__text"><strong>Reason:</strong> <?= e($report["reason"]) ?></p>
                    <?php if ($target): ?>
                        <blockquote class="approval__quote"><a href="<?= e(url($target["link"])) ?>"><?= e(mb_strimwidth($target["text"], 0, 240, "…")) ?></a></blockquote>
                    <?php else: ?>
                        <p class="sc-note-row__by">The content has already been deleted.</p>
                    <?php endif; ?>
                </div>
                <div class="approval__actions">
                    <form method="post" action="<?= e(url("/admin/moderation/" . $id . "/dismiss")) ?>">
                        <?= csrf_field() ?>
                        <?= component("button", ["label" => "Dismiss", "variant" => "outline", "size" => "sm", "type" => "submit", "class" => "sc-btn-quiet"]) ?>
                    </form>
                    <?php if ($target): ?>
                        <form method="post" action="<?= e(url("/admin/moderation/" . $id . "/hide")) ?>" data-confirm="Remove this content from the site?">
                            <?= csrf_field() ?>
                            <?= component("button", ["label" => "Remove content", "variant" => "danger", "size" => "sm", "type" => "submit"]) ?>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="sc-card"><?= component("empty-state", ["icon" => "check2-circle", "title" => "No open reports", "text" => "Reported content will appear here."]) ?></div>
<?php endif; ?>
