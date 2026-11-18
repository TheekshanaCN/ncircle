<?php /** views/admin/subjects.php  -  NSBM modules */ ?>
<?= component("page-header", ["title" => "Subjects", "subtitle" => "Modules students can file notes and questions under."]) ?>

<div class="sc-form-layout sc-form-layout--side-first">
    <form class="sc-panel sc-panel--tight" method="post" action="<?= e(url("/admin/subjects")) ?>">
        <?= csrf_field() ?>
        <h2 class="sc-panel__title mb-3">Add a subject</h2>
        <?= component("form-field", ["name" => "code", "label" => "Module code", "placeholder" => "CS204", "required" => true, "attrs" => ["maxlength" => 12]]) ?>
        <?= component("form-field", ["name" => "name", "label" => "Name", "placeholder" => "Database Systems", "required" => true, "attrs" => ["maxlength" => 100]]) ?>
        <?= component("form-field", ["name" => "faculty", "label" => "Faculty", "type" => "select", "placeholder" => "Choose a faculty", "options" => config("app.faculties"), "required" => true]) ?>
        <?= component("form-field", ["name" => "year", "label" => "Year", "type" => "select", "placeholder" => "Choose a year", "options" => config("app.years"), "required" => true]) ?>
        <?= component("button", ["label" => "Add subject", "type" => "submit", "variant" => "dark", "size" => "sm"]) ?>
    </form>

    <div class="sc-table-wrap">
        <table class="table sc-table align-middle">
            <thead><tr><th scope="col">Code</th><th scope="col">Name</th><th scope="col">Faculty</th><th scope="col">Year</th><th scope="col">Notes</th><th scope="col" class="text-end">Status</th></tr></thead>
            <tbody>
            <?php foreach ($subjects as $subject): ?>
                <tr class="<?= $subject["is_active"] ? "" : "sc-table__row--off" ?>">
                    <td><span class="sc-chip sc-chip--mono"><?= e($subject["code"]) ?></span></td>
                    <td><?= e($subject["name"]) ?></td>
                    <td><?= e($subject["faculty"]) ?></td>
                    <td><?= (int) $subject["year"] ?></td>
                    <td><?= (int) $subject["notes"] ?></td>
                    <td class="text-end">
                        <form method="post" action="<?= e(url("/admin/subjects/" . (int) $subject["id"] . "/toggle")) ?>">
                            <?= csrf_field() ?>
                            <button class="sc-text-btn" type="submit"><?= $subject["is_active"] ? "Archive" : "Restore" ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
