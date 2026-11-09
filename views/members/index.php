<?php /** views/members/index.php  -  Find Members request board */ ?>
<?php include __DIR__ . "/_tabs.php"; ?>

<form class="browse-bar" method="get" action="<?= e(url("/members")) ?>">
    <?php if ($filters["mine"]): ?><input type="hidden" name="mine" value="1"><?php endif; ?>
    <div class="browse-bar__search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <label class="visually-hidden" for="requestQ">Search requests</label>
        <input id="requestQ" type="search" name="q" value="<?= e($filters["q"]) ?>" placeholder="Search requests and skills">
    </div>
    <div class="browse-bar__sort">
        <label class="visually-hidden" for="requestType">Type</label>
        <select id="requestType" class="form-select" name="type" data-autosubmit>
            <option value="">All types</option>
            <?php foreach (config("app.help_types") as $value => $label): ?>
                <option value="<?= e($value) ?>"<?= $filters["type"] === $value ? " selected" : "" ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="browse-bar__sort">
        <label class="visually-hidden" for="requestFaculty">Looking in faculty</label>
        <select id="requestFaculty" class="form-select" name="faculty" data-autosubmit>
            <option value="">Any faculty</option>
            <?php foreach (config("app.faculties") as $value => $label): ?>
                <option value="<?= e($value) ?>"<?= $filters["faculty"] === $value ? " selected" : "" ?>><?= e($value) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</form>

<?php if ($result["rows"]): ?>
    <div class="sc-stack">
        <?php foreach ($result["rows"] as $request): ?><?= component("request-card", ["request" => $request]) ?><?php endforeach; ?>
    </div>
    <div class="mt-4"><?= component("pagination", ["page" => $page, "pages" => $result["pages"], "baseUrl" => url("/members"), "query" => array_filter(["q" => $filters["q"], "type" => $filters["type"], "faculty" => $filters["faculty"], "mine" => $filters["mine"] ? "1" : ""])]) ?></div>
<?php else: ?>
    <div class="sc-card"><?= component("empty-state", ["icon" => "person-raised-hand", "title" => "No requests found", "text" => "Need help with research or a project? Ask here.", "actionLabel" => "Post a request", "actionHref" => url("/members/requests/create")]) ?></div>
<?php endif; ?>
