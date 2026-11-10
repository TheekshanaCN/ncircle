<?php /** views/search/index.php  -  results for the top bar search */ ?>
<?= component("page-header", [
    "title" => $q !== "" ? "Results for “" . $q . "”" : "Search",
    "subtitle" => $q !== "" ? "Notes, discussions, groups and meetings in your faculty that match." : "Type in the search bar above to look across Ncircle.",
]) ?>

<?php if ($q !== ""): ?>
    <?php $nothing = !$notes["total"] && !$threads["total"] && !$groups["total"] && !$sessions["total"]; ?>
    <?php if ($nothing): ?>
        <div class="sc-card"><?= component("empty-state", ["icon" => "search", "title" => "Nothing found", "text" => "Try a module code, or a shorter word."]) ?></div>
    <?php endif; ?>

    <?php if ($notes["total"]): ?>
        <section class="sc-result-group">
            <header class="sc-panel__head"><h2 class="sc-panel__title">Notes (<?= (int) $notes["total"] ?>)</h2><a class="sc-panel__link" href="<?= e(url("/resources?q=" . rawurlencode($q))) ?>">See all <i class="bi bi-arrow-right" aria-hidden="true"></i></a></header>
            <div class="browse-list__items"><?php foreach ($notes["rows"] as $r): ?><?= component("note-card", ["resource" => $r]) ?><?php endforeach; ?></div>
        </section>
    <?php endif; ?>

    <?php if ($threads["total"]): ?>
        <section class="sc-result-group">
            <header class="sc-panel__head"><h2 class="sc-panel__title">Discussions (<?= (int) $threads["total"] ?>)</h2><a class="sc-panel__link" href="<?= e(url("/forum?q=" . rawurlencode($q))) ?>">See all <i class="bi bi-arrow-right" aria-hidden="true"></i></a></header>
            <div class="sc-stack"><?php foreach ($threads["rows"] as $t): ?><?= component("thread-card", ["question" => $t]) ?><?php endforeach; ?></div>
        </section>
    <?php endif; ?>

    <?php if ($groups["total"]): ?>
        <section class="sc-result-group">
            <header class="sc-panel__head"><h2 class="sc-panel__title">Study groups (<?= (int) $groups["total"] ?>)</h2></header>
            <div class="sc-card-grid"><?php foreach ($groups["rows"] as $g): ?><?= component("group-card", ["group" => $g]) ?><?php endforeach; ?></div>
        </section>
    <?php endif; ?>

    <?php if ($sessions["total"]): ?>
        <section class="sc-result-group">
            <header class="sc-panel__head"><h2 class="sc-panel__title">Meetings (<?= (int) $sessions["total"] ?>)</h2></header>
            <div class="sc-card-grid"><?php foreach ($sessions["rows"] as $s): ?><?= component("pod-card", ["session" => $s]) ?><?php endforeach; ?></div>
        </section>
    <?php endif; ?>
<?php endif; ?>
