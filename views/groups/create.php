<?php /** views/groups/create.php  -  create a study group */ ?>
<?= component("page-header", [
    "title" => "Create a study group",
    "subtitle" => "You become the owner and first member.",
    "actions" => component("button", ["label" => "Back to groups", "icon" => "arrow-left", "variant" => "outline", "size" => "sm", "href" => url("/groups"), "class" => "sc-btn-quiet"]),
]) ?>
<div class="sc-form-narrow">
    <form class="sc-panel" method="post" action="<?= e(url("/groups")) ?>">
        <?= csrf_field() ?>
        <?= component("form-field", ["name" => "name", "label" => "Group name", "placeholder" => "CS204 Exam Sprint Crew", "required" => true, "attrs" => ["maxlength" => 100]]) ?>
        <?= component("form-field", ["name" => "subject_id", "label" => "Subject", "type" => "select", "placeholder" => "Choose a subject", "options" => $subjects, "required" => true]) ?>
        <?= component("form-field", ["name" => "description", "label" => "What is this group for?", "type" => "textarea", "rows" => 4, "required" => true, "attrs" => ["maxlength" => 500]]) ?>
        <?= component("form-field", ["name" => "max_members", "label" => "Member limit", "type" => "number", "value" => old("max_members", "12"), "required" => true, "attrs" => ["min" => 2, "max" => 100]]) ?>
        <?= component("button", ["label" => "Create group", "type" => "submit", "variant" => "dark"]) ?>
    </form>
</div>
