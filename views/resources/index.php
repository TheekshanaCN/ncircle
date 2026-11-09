<?php
/** views/resources/index.php  -  Browse notes (Figma frame "Browse notes") */
$byId = array_column($subjects, null, "id");
$categoryBySlug = array_column($categories, null, "slug");
$allCount = array_sum($categoryCounts);

// Removable chips for everything currently applied
$chips = [];
foreach ($filters["subjects"] as $id) {
    if (isset($byId[$id])) {
        $chips[] = [$byId[$id]["name"], query_url(["subject" => array_values(array_diff($filters["subjects"], [$id])), "page" => null])];
    }
}
if ($filters["year"]) { $chips[] = ["Year " . $filters["year"], query_url(["year" => null, "page" => null])]; }
if ($filters["category"] !== "") { $chips[] = [$categoryBySlug[$filters["category"]]["name"], query_url(["category" => null, "page" => null])]; }
if ($filters["format"] !== "") { $chips[] = [strtoupper($filters["format"]), query_url(["format" => null, "page" => null])]; }
if ($filters["min_rating"] !== "") { $chips[] = [$filters["min_rating"] . "+ stars", query_url(["min_rating" => null, "page" => null])]; }
if ($filters["faculty"] !== "" && Auth::facultyScope() === null) { $chips[] = [$filters["faculty"], query_url(["faculty" => null, "page" => null])]; }
$clearUrl = url("/resources") . ($filters["q"] !== "" ? "?q=" . rawurlencode($filters["q"]) : "");
?>
<?= component("page-header", [
    "title" => "Browse notes",
    "titleTag" => "+" . compact_number((int) $verified) . " Verified",
    "subtitle" => "Find lecture notes, study guides and past papers shared by " . (Auth::facultyScope() !== null ? "Faculty of " . Auth::facultyScope() : "NSBM") . " students.",
]) ?>

<form id="browseForm" method="get" action="<?= e(url("/resources")) ?>"></form>

<div class="browse-bar">
    <div class="browse-bar__search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <label class="visually-hidden" for="browseQ">Search notes</label>
        <input id="browseQ" form="browseForm" type="search" name="q" value="<?= e($filters["q"]) ?>" placeholder="Search notes, subjects, and topics">
    </div>
    <button class="browse-bar__btn" type="button" data-bs-toggle="collapse" data-bs-target="#browseFilters" aria-controls="browseFilters" aria-expanded="true">
        <i class="bi bi-sliders" aria-hidden="true"></i> Filter
        <?php if ($chips): ?><span class="browse-bar__count"><?= count($chips) ?></span><?php endif; ?>
    </button>
    <div class="browse-bar__sort">
        <label class="visually-hidden" for="browseSort">Sort notes</label>
        <select id="browseSort" class="form-select" form="browseForm" name="sort" data-autosubmit>
            <?php foreach (Resource::SORTS as $value => $label): ?>
                <option value="<?= e($value) ?>"<?= $filters["sort"] === $value ? " selected" : "" ?>>Sort: <?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<nav class="browse-tabs" aria-label="Categories">
    <a class="browse-tabs__tab<?= $filters["category"] === "" ? " is-active" : "" ?>" href="<?= e(query_url(["category" => null, "page" => null])) ?>">All (<?= number_format($allCount) ?>)</a>
    <?php foreach ($categories as $category): ?>
        <a class="browse-tabs__tab<?= $filters["category"] === $category["slug"] ? " is-active" : "" ?>" href="<?= e(query_url(["category" => $category["slug"], "page" => null])) ?>"><?= e($category["plural"]) ?> (<?= number_format((int) ($categoryCounts[$category["slug"]] ?? 0)) ?>)</a>
    <?php endforeach; ?>
</nav>

<?php if ($chips): ?>
    <div class="browse-chips">
        <?php foreach ($chips as [$label, $href]): ?>
            <a class="browse-chip" href="<?= e($href) ?>"><?= e($label) ?> <i class="bi bi-x-lg" aria-hidden="true"></i><span class="visually-hidden"> (remove filter)</span></a>
        <?php endforeach; ?>
        <a class="browse-chips__clear" href="<?= e($clearUrl) ?>">Clear all</a>
    </div>
<?php endif; ?>

<div class="browse-layout">
    <div class="browse-list">
        <div id="noteList" class="browse-list__items">
            <?php foreach ($result["rows"] as $resource): ?>
                <?= component("note-card", ["resource" => $resource]) ?>
            <?php endforeach; ?>
        </div>

        <?php if (!$result["rows"]): ?>
            <div class="sc-card">
                <?= component("empty-state", [
                    "icon" => "search",
                    "title" => "No notes match those filters",
                    "text" => "Try fewer filters, or share the note you were looking for.",
                    "actionLabel" => "Share a note",
                    "actionHref" => url("/resources/create"),
                ]) ?>
            </div>
        <?php endif; ?>

        <?php if ($result["page"] < $result["pages"]): ?>
            <div class="browse-more">
                <a class="btn btn-outline-primary btn-xs sc-btn-quiet" href="<?= e(query_url(["page" => $result["page"] + 1])) ?>" data-load-more="#noteList" data-last-page="<?= (int) $result["pages"] ?>"><i class="bi bi-chevron-down" aria-hidden="true"></i> Load more notes</a>
            </div>
            <div class="browse-skeleton" data-load-skeleton hidden aria-hidden="true">
                <span class="browse-skeleton__thumb"></span>
                <span class="browse-skeleton__lines"><i></i><i></i></span>
                <span class="browse-skeleton__btn"></span>
            </div>
        <?php endif; ?>
    </div>

    <aside class="collapse show browse-filters" id="browseFilters" aria-label="Filters" data-collapse-on-phone>
        <div class="browse-filters__card">
            <header class="browse-filters__head">
                <h2><i class="bi bi-sliders" aria-hidden="true"></i> Filters</h2>
                <a href="<?= e($clearUrl) ?>">Reset</a>
            </header>

            <fieldset>
                <legend>Subject</legend>
                <?php foreach (array_slice($subjects, 0, 8) as $subject): $checked = in_array((int) $subject["id"], $filters["subjects"], true); ?>
                    <label class="browse-check">
                        <input class="form-check-input" type="checkbox" form="browseForm" name="subject[]" value="<?= (int) $subject["id"] ?>"<?= $checked ? " checked" : "" ?>>
                        <span><?= e($subject["name"]) ?></span>
                        <small><?= (int) ($subjectCounts[$subject["id"]] ?? 0) ?></small>
                    </label>
                <?php endforeach; ?>
            </fieldset>

            <fieldset>
                <legend>Category</legend>
                <div class="browse-pills">
                    <?php foreach ($categories as $category): ?>
                        <label class="browse-pill"><input type="radio" form="browseForm" name="category" value="<?= e($category["slug"]) ?>"<?= $filters["category"] === $category["slug"] ? " checked" : "" ?>><span><?= e($category["name"]) ?></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <fieldset>
                <legend>Academic year / semester</legend>
                <div class="browse-pills browse-pills--even">
                    <?php for ($y = 1; $y <= 4; $y++): ?>
                        <label class="browse-pill"><input type="radio" form="browseForm" name="year" value="<?= $y ?>"<?= $filters["year"] === $y ? " checked" : "" ?>><span>Yr <?= $y ?></span></label>
                    <?php endfor; ?>
                </div>
            </fieldset>

            <fieldset>
                <legend>Resource format</legend>
                <div class="browse-pills">
                    <?php foreach (["pdf", "docx", "pptx"] as $format): ?>
                        <label class="browse-pill"><input type="radio" form="browseForm" name="format" value="<?= $format ?>"<?= $filters["format"] === $format ? " checked" : "" ?>><span><?= strtoupper($format) ?></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <fieldset>
                <legend>Minimum rating</legend>
                <div class="browse-pills browse-pills--even">
                    <label class="browse-pill browse-pill--square"><input type="radio" form="browseForm" name="min_rating" value="4.5"<?= $filters["min_rating"] === "4.5" ? " checked" : "" ?>><span><i class="bi bi-star" aria-hidden="true"></i> 4.5+</span></label>
                    <label class="browse-pill browse-pill--square"><input type="radio" form="browseForm" name="min_rating" value="4"<?= $filters["min_rating"] === "4" ? " checked" : "" ?>><span>4.0+</span></label>
                    <label class="browse-pill browse-pill--square"><input type="radio" form="browseForm" name="min_rating" value=""<?= $filters["min_rating"] === "" ? " checked" : "" ?>><span>All</span></label>
                </div>
            </fieldset>

            <footer class="browse-filters__foot">
                <a class="btn btn-outline-primary btn-xs sc-btn-quiet" href="<?= e($clearUrl) ?>">Reset</a>
                <button class="btn btn-dark btn-xs flex-grow-1" type="submit" form="browseForm">Apply filters</button>
            </footer>
        </div>
    </aside>
</div>
