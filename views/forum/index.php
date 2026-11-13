<?php
/** views/forum/index.php  -  discussion forum list */
$tabs = ["" => "All", "unanswered" => "Needs answer", "active" => "Active", "solved" => "Solved"];
?>
<?= component("page-header", [
    "title" => "Discussion forum",
    "subtitle" => "Ask coursework questions and get answers from peers across your faculty.",
    "actions" => component("button", ["label" => "Ask question", "icon" => "plus-lg", "variant" => "dark", "size" => "sm", "href" => url("/forum/create")]),
]) ?>

<form class="browse-bar" method="get" action="<?= e(url("/forum")) ?>">
    <div class="browse-bar__search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <label class="visually-hidden" for="forumQ">Search questions</label>
        <input id="forumQ" type="search" name="q" value="<?= e($filters["q"]) ?>" placeholder="Search questions">
    </div>
    <div class="browse-bar__sort">
        <label class="visually-hidden" for="forumSubject">Subject</label>
        <select id="forumSubject" class="form-select" name="subject" data-autosubmit>
            <option value="">All subjects</option>
            <?php foreach ($subjects as $value => $label): ?>
                <option value="<?= (int) $value ?>"<?= $filters["subject"] === (int) $value ? " selected" : "" ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($filters["status"] !== ""): ?><input type="hidden" name="status" value="<?= e($filters["status"]) ?>"><?php endif; ?>
</form>

<nav class="browse-tabs" aria-label="Question status">
    <?php foreach ($tabs as $value => $label): ?>
        <a class="browse-tabs__tab<?= $filters["status"] === $value ? " is-active" : "" ?>" href="<?= e(query_url(["status" => $value, "page" => null])) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>

<?php if ($result["rows"]): ?>
    <div class="sc-stack">
        <?php foreach ($result["rows"] as $question): ?><?= component("thread-card", ["question" => $question]) ?><?php endforeach; ?>
    </div>
    <div class="mt-4"><?= component("pagination", ["page" => $page, "pages" => $result["pages"], "baseUrl" => url("/forum"), "query" => array_filter(["q" => $filters["q"], "subject" => $filters["subject"], "status" => $filters["status"]])]) ?></div>
<?php else: ?>
    <div class="sc-card"><?= component("empty-state", ["icon" => "chat-left-text", "title" => "No questions found", "text" => "Be the first to ask about this.", "actionLabel" => "Ask question", "actionHref" => url("/forum/create")]) ?></div>
<?php endif; ?>
