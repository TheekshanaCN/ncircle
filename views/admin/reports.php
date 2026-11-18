<?php /** views/admin/reports.php  -  usage reports */ ?>
<?= component("page-header", [
    "title" => "Reports",
    "subtitle" => "How the campus circle is using Ncircle.",
    "actions" => component("button", ["label" => "Export subjects CSV", "icon" => "filetype-csv", "variant" => "outline", "size" => "sm", "href" => url("/admin/reports/export"), "class" => "sc-btn-quiet"]),
]) ?>

<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3"><?= component("stat-card", ["icon" => "download", "value" => $overview["downloads"], "label" => "Total downloads"]) ?></div>
    <div class="col-6 col-xl-3"><?= component("stat-card", ["icon" => "chat-left-text", "value" => $overview["questions"], "label" => "Questions asked"]) ?></div>
    <div class="col-6 col-xl-3"><?= component("stat-card", ["icon" => "people", "value" => $overview["groups"], "label" => "Study groups"]) ?></div>
    <div class="col-6 col-xl-3"><?= component("stat-card", ["icon" => "calendar-event", "value" => $overview["sessions"], "label" => "Upcoming meetings"]) ?></div>
</div>

<div class="dash-split">
    <section aria-labelledby="top-title">
        <h2 class="sc-panel__title answers-title" id="top-title">Most downloaded notes</h2>
        <div class="sc-table-wrap">
            <table class="table sc-table align-middle">
                <thead><tr><th scope="col">Note</th><th scope="col">Subject</th><th scope="col" class="text-end">Rating</th><th scope="col" class="text-end">Downloads</th></tr></thead>
                <tbody>
                <?php foreach ($topNotes as $note): ?>
                    <tr><td><a href="<?= e(url("/resources/" . (int) $note["id"])) ?>"><?= e($note["title"]) ?></a></td><td><?= e($note["subject"]) ?></td><td class="text-end"><?= e($note["rating"]) ?></td><td class="text-end"><?= number_format((int) $note["downloads_count"]) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section aria-labelledby="people-title">
        <h2 class="sc-panel__title answers-title" id="people-title">Top contributors</h2>
        <div class="sc-table-wrap">
            <table class="table sc-table align-middle">
                <thead><tr><th scope="col">Student</th><th scope="col">Faculty</th><th scope="col" class="text-end">Notes</th><th scope="col" class="text-end">Answers</th></tr></thead>
                <tbody>
                <?php foreach ($contributors as $person): ?>
                    <tr><td><a href="<?= e(url("/profile/" . (int) $person["id"])) ?>"><?= e($person["name"]) ?></a></td><td><?= e($person["faculty"]) ?></td><td class="text-end"><?= (int) $person["notes"] ?></td><td class="text-end"><?= (int) $person["answers"] ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<section aria-labelledby="subject-title" class="mt-4">
    <h2 class="sc-panel__title answers-title" id="subject-title">Activity by subject</h2>
    <div class="sc-table-wrap">
        <table class="table sc-table align-middle">
            <thead><tr><th scope="col">Code</th><th scope="col">Subject</th><th scope="col">Faculty</th><th scope="col" class="text-end">Notes</th><th scope="col" class="text-end">Downloads</th></tr></thead>
            <tbody>
            <?php foreach ($subjects as $subject): ?>
                <tr><td><span class="sc-chip sc-chip--mono"><?= e($subject["code"]) ?></span></td><td><?= e($subject["name"]) ?></td><td><?= e($subject["faculty"]) ?></td><td class="text-end"><?= (int) $subject["notes"] ?></td><td class="text-end"><?= number_format((int) $subject["downloads"]) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
