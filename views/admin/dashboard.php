<?php /** views/admin/dashboard.php */ ?>
<?= component("page-header", ["title" => "Admin overview", "subtitle" => "What needs attention across Ncircle today."]) ?>

<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3"><?= component("stat-card", ["icon" => "hourglass-split", "value" => $overview["pending"], "label" => "Notes to review", "foot" => ["Approval queue"]]) ?></div>
    <div class="col-12 col-sm-6 col-xl-3"><?= component("stat-card", ["icon" => "flag", "value" => $overview["reports"], "label" => "Open reports", "foot" => ["Moderation queue"]]) ?></div>
    <div class="col-12 col-sm-6 col-xl-3"><?= component("stat-card", ["icon" => "people", "value" => $overview["users"], "label" => "Students", "foot" => ["Registered accounts"]]) ?></div>
    <div class="col-12 col-sm-6 col-xl-3"><?= component("stat-card", ["icon" => "file-earmark-text", "value" => $overview["notes"], "label" => "Approved notes", "foot" => [number_format($overview["downloads"]) . " downloads"]]) ?></div>
</div>

<div class="dash-split">
    <section class="sc-panel sc-panel--tight" aria-labelledby="queue-title">
        <header class="sc-panel__head">
            <h2 class="sc-panel__title" id="queue-title">Waiting for approval</h2>
            <a class="sc-panel__link" href="<?= e(url("/admin/approvals")) ?>">Open queue <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </header>
        <?php if ($pending): ?>
            <ul class="admin-list">
                <?php foreach ($pending as $note): ?>
                    <li><a href="<?= e(url("/resources/" . (int) $note["id"])) ?>"><?= e($note["title"]) ?></a><span><?= e($note["subject"]) ?> &middot; <?= e($note["uploader"]) ?> &middot; <?= e(time_ago($note["created_at"])) ?></span></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?= component("empty-state", ["icon" => "check2-circle", "title" => "Queue is clear"]) ?>
        <?php endif; ?>
    </section>

    <section class="sc-panel sc-panel--tight" aria-labelledby="flags-title">
        <header class="sc-panel__head">
            <h2 class="sc-panel__title" id="flags-title">Recent reports</h2>
            <a class="sc-panel__link" href="<?= e(url("/admin/moderation")) ?>">Open queue <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </header>
        <?php if ($reports): ?>
            <ul class="admin-list">
                <?php foreach ($reports as $report): ?>
                    <li><strong><?= e(ucfirst(str_replace("_", " ", $report["target_type"]))) ?>:</strong> <?= e(mb_strimwidth($report["reason"], 0, 80, "…")) ?><span>by <?= e($report["reporter"]) ?> &middot; <?= e(time_ago($report["created_at"])) ?></span></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?= component("empty-state", ["icon" => "check2-circle", "title" => "No open reports"]) ?>
        <?php endif; ?>
    </section>
</div>
