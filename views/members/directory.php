<?php /** views/members/directory.php  -  browse students across all faculties */ ?>
<?php include __DIR__ . "/_tabs.php"; ?>

<form class="browse-bar" method="get" action="<?= e(url("/members/directory")) ?>">
    <div class="browse-bar__search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <label class="visually-hidden" for="memberQ">Search members</label>
        <input id="memberQ" type="search" name="q" value="<?= e($filters["q"]) ?>" placeholder="Search by name or skill">
    </div>
    <div class="browse-bar__sort">
        <label class="visually-hidden" for="memberFaculty">Faculty</label>
        <select id="memberFaculty" class="form-select" name="faculty" data-autosubmit>
            <option value="">All faculties</option>
            <?php foreach (config("app.faculties") as $value => $label): ?>
                <option value="<?= e($value) ?>"<?= $filters["faculty"] === $value ? " selected" : "" ?>><?= e($value) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <label class="browse-bar__btn members-open">
        <input class="form-check-input m-0" type="checkbox" name="open" value="1"<?= $filters["open"] ? " checked" : "" ?> data-autosubmit> Open to help
    </label>
</form>

<?php if ($result["rows"]): ?>
    <div class="sc-card-grid">
        <?php foreach ($result["rows"] as $member): ?><?= component("member-card", ["member" => $member]) ?><?php endforeach; ?>
    </div>
    <div class="mt-4"><?= component("pagination", ["page" => $page, "pages" => $result["pages"], "baseUrl" => url("/members/directory"), "query" => array_filter(["q" => $filters["q"], "faculty" => $filters["faculty"], "open" => $filters["open"] ? "1" : ""])]) ?></div>
<?php else: ?>
    <div class="sc-card"><?= component("empty-state", ["icon" => "people", "title" => "No members found", "text" => "Try a different skill or faculty."]) ?></div>
<?php endif; ?>
