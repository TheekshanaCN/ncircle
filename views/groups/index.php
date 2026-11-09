<?php /** views/groups/index.php  -  study groups list */ ?>
<?= component("page-header", [
    "title" => "Study groups",
    "subtitle" => "Revise together with peers on the same module.",
    "actions" => component("button", ["label" => "Meetings", "icon" => "calendar-event", "variant" => "outline", "size" => "sm", "href" => url("/sessions"), "class" => "sc-btn-quiet"])
        . component("button", ["label" => "Create group", "icon" => "plus-lg", "variant" => "dark", "size" => "sm", "href" => url("/groups/create")]),
]) ?>

<form class="browse-bar" method="get" action="<?= e(url("/groups")) ?>">
    <div class="browse-bar__search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <label class="visually-hidden" for="groupQ">Search groups</label>
        <input id="groupQ" type="search" name="q" value="<?= e($filters["q"]) ?>" placeholder="Search groups">
    </div>
    <div class="browse-bar__sort">
        <label class="visually-hidden" for="groupSubject">Subject</label>
        <select id="groupSubject" class="form-select" name="subject" data-autosubmit>
            <option value="">All subjects</option>
            <?php foreach ($subjects as $value => $label): ?>
                <option value="<?= (int) $value ?>"<?= $filters["subject"] === (int) $value ? " selected" : "" ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($filters["mine"]): ?><input type="hidden" name="mine" value="1"><?php endif; ?>
</form>

<nav class="browse-tabs" aria-label="Group filter">
    <a class="browse-tabs__tab<?= !$filters["mine"] ? " is-active" : "" ?>" href="<?= e(query_url(["mine" => null, "page" => null])) ?>">All groups</a>
    <a class="browse-tabs__tab<?= $filters["mine"] ? " is-active" : "" ?>" href="<?= e(query_url(["mine" => "1", "page" => null])) ?>">My groups</a>
</nav>

<?php if ($result["rows"]): ?>
    <div class="sc-card-grid">
        <?php foreach ($result["rows"] as $group): ?><?= component("group-card", ["group" => $group]) ?><?php endforeach; ?>
    </div>
    <div class="mt-4"><?= component("pagination", ["page" => $page, "pages" => $result["pages"], "baseUrl" => url("/groups"), "query" => array_filter(["q" => $filters["q"], "subject" => $filters["subject"], "mine" => $filters["mine"] ? "1" : ""])]) ?></div>
<?php else: ?>
    <div class="sc-card"><?= component("empty-state", ["icon" => "people", "title" => "No groups found", "text" => "Start one for your module.", "actionLabel" => "Create group", "actionHref" => url("/groups/create")]) ?></div>
<?php endif; ?>
