<?php
/** views/sessions/index.php  -  meetings in your faculty, plus your groups' meetings */
$modes = config("app.session_modes");
?>
<?= component("page-header", [
    "title" => "Meetings",
    "subtitle" => "Online, physical and hybrid study meetings in your faculty.",
    "actions" => component("button", ["label" => "Study groups", "icon" => "people", "variant" => "outline", "size" => "sm", "href" => url("/groups"), "class" => "sc-btn-quiet"])
        . component("button", ["label" => "Schedule meeting", "icon" => "plus-lg", "variant" => "dark", "size" => "sm", "href" => url("/sessions/create")]),
]) ?>

<nav class="browse-tabs" aria-label="Meeting type">
    <a class="browse-tabs__tab<?= $filters["mode"] === "" && !$filters["mine"] && $filters["when"] === "" ? " is-active" : "" ?>" href="<?= e(url("/sessions")) ?>">All upcoming</a>
    <?php foreach ($modes as $value => $label): ?>
        <a class="browse-tabs__tab<?= $filters["mode"] === $value ? " is-active" : "" ?>" href="<?= e(url("/sessions?mode=" . $value)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
    <a class="browse-tabs__tab<?= $filters["mine"] ? " is-active" : "" ?>" href="<?= e(url("/sessions?mine=1")) ?>">I'm going</a>
    <a class="browse-tabs__tab<?= $filters["when"] === "past" ? " is-active" : "" ?>" href="<?= e(url("/sessions?when=past")) ?>">Past</a>
</nav>

<?php if ($result["rows"]): ?>
    <div class="sc-card-grid">
        <?php foreach ($result["rows"] as $session): ?><?= component("pod-card", ["session" => $session]) ?><?php endforeach; ?>
    </div>
    <div class="mt-4"><?= component("pagination", ["page" => $page, "pages" => $result["pages"], "baseUrl" => url("/sessions"), "query" => array_filter(["mode" => $filters["mode"], "mine" => $filters["mine"] ? "1" : "", "when" => $filters["when"]])]) ?></div>
<?php else: ?>
    <div class="sc-card"><?= component("empty-state", ["icon" => "calendar-event", "title" => "No meetings here", "text" => "Schedule one and invite your batch.", "actionLabel" => "Schedule meeting", "actionHref" => url("/sessions/create")]) ?></div>
<?php endif; ?>
