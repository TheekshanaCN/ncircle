<?php /** views/profile/space.php  -  personal study space: goal, saved notes, uploads, groups */ ?>
<?= component("page-header", [
    "title" => "Personal study space",
    "subtitle" => "Your weekly goal, saved notes and upload approvals, in one place.",
    "actions" => component("button", ["label" => "Share a note", "icon" => "plus-lg", "variant" => "dark", "size" => "sm", "href" => url("/resources/create")]),
]) ?>

<div class="space-top">
    <section class="sc-panel" aria-labelledby="goal-title">
        <h2 class="sc-panel__title" id="goal-title"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i> Weekly study goal</h2>
        <p class="space-goal__value"><?= (int) $goal["percent"] ?>%</p>
        <p class="sc-panel__sub"><?= e(format_minutes($goal["minutes"])) ?> of <?= e(format_minutes($goal["target"])) ?> this week &middot; <?= (int) $goal["streak"] ?> day streak</p>
        <div class="sc-progress my-3" role="progressbar" aria-valuenow="<?= (int) $goal["percent"] ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Weekly study goal"><span style="width: <?= (int) $goal["percent"] ?>%"></span></div>
        <form class="space-goal__form" method="post" action="<?= e(url("/space/goal")) ?>">
            <?= csrf_field() ?>
            <?= component("form-field", ["name" => "goal_hours", "label" => "Hours per week", "type" => "number", "value" => old("goal_hours", (int) round($goal["target"] / 60)), "required" => true, "attrs" => ["min" => 1, "max" => 100]]) ?>
            <?= component("button", ["label" => "Update goal", "type" => "submit", "variant" => "outline", "size" => "sm", "class" => "sc-btn-quiet"]) ?>
        </form>
    </section>

    <section class="sc-panel" aria-labelledby="log-title">
        <h2 class="sc-panel__title" id="log-title"><i class="bi bi-stopwatch" aria-hidden="true"></i> Log study time</h2>
        <form method="post" action="<?= e(url("/space/log")) ?>" class="mt-3">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-sm-6"><?= component("form-field", ["name" => "minutes", "label" => "Minutes", "type" => "number", "placeholder" => "45", "required" => true, "attrs" => ["min" => 1, "max" => 720]]) ?></div>
                <div class="col-sm-6"><?= component("form-field", ["name" => "logged_on", "label" => "Date", "type" => "date", "value" => old("logged_on", date("Y-m-d")), "required" => true, "attrs" => ["max" => date("Y-m-d")]]) ?></div>
            </div>
            <?= component("form-field", ["name" => "note", "label" => "What did you cover? (optional)", "attrs" => ["maxlength" => 160]]) ?>
            <?= component("button", ["label" => "Log time", "type" => "submit", "variant" => "dark", "size" => "sm"]) ?>
        </form>
        <?php if ($logs): ?>
            <ul class="space-logs">
                <?php foreach (array_slice($logs, 0, 4) as $log): ?>
                    <li><strong><?= e(format_minutes((int) $log["minutes"])) ?></strong> <span><?= e($log["note"] ?: "Study time") ?></span> <time><?= e(date("M j", strtotime($log["logged_on"]))) ?></time></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>

<section aria-labelledby="saved-title" class="space-section">
    <h2 class="sc-panel__title answers-title" id="saved-title"><i class="bi bi-bookmark" aria-hidden="true"></i> Saved notes (<?= count($bookmarks) ?>)</h2>
    <?php if ($bookmarks): ?>
        <div class="browse-list__items">
            <?php foreach ($bookmarks as $note): ?><?= component("note-card", ["resource" => $note]) ?><?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="sc-card"><?= component("empty-state", ["icon" => "bookmark", "title" => "Nothing saved yet", "text" => "Tap the bookmark on any note to keep it here.", "actionLabel" => "Browse notes", "actionHref" => url("/resources")]) ?></div>
    <?php endif; ?>
</section>

<section aria-labelledby="uploads-title" class="space-section">
    <h2 class="sc-panel__title answers-title" id="uploads-title"><i class="bi bi-upload" aria-hidden="true"></i> My uploads (<?= count($uploads) ?>)</h2>
    <?php if ($uploads): ?>
        <div class="sc-table-wrap">
            <table class="table sc-table align-middle">
                <thead><tr><th scope="col">Note</th><th scope="col">Subject</th><th scope="col">Status</th><th scope="col" class="text-end">Downloads</th></tr></thead>
                <tbody>
                <?php foreach ($uploads as $upload): ?>
                    <tr>
                        <td><a href="<?= e(url("/resources/" . (int) $upload["id"])) ?>"><?= e($upload["title"]) ?></a><?php if ($upload["reject_reason"]): ?><small class="d-block sc-table__sub"><?= e($upload["reject_reason"]) ?></small><?php endif; ?></td>
                        <td><?= e($upload["subject"]) ?></td>
                        <td><?= component("status-badge", ["status" => $upload["status"]]) ?></td>
                        <td class="text-end"><?= number_format((int) $upload["downloads_count"]) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="sc-card"><?= component("empty-state", ["icon" => "upload", "title" => "No uploads yet", "text" => "Share a note and track its approval here.", "actionLabel" => "Share a note", "actionHref" => url("/resources/create")]) ?></div>
    <?php endif; ?>
</section>

<?php if ($groups): ?>
<section aria-labelledby="my-groups" class="space-section">
    <h2 class="sc-panel__title answers-title" id="my-groups"><i class="bi bi-people" aria-hidden="true"></i> My groups</h2>
    <div class="sc-card-grid"><?php foreach ($groups as $group): ?><?= component("group-card", ["group" => $group]) ?><?php endforeach; ?></div>
</section>
<?php endif; ?>
