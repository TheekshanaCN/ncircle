<?php
/** views/groups/create.php  -  create or edit a study group. $group is null when creating. */
$editing = $group !== null;
$value = fn (string $key, $fallback = "") => old($key, $editing ? $group[$key] : $fallback);
?>
<?= component("page-header", [
    "title" => $editing ? "Edit study group" : "Create a study group",
    "subtitle" => $editing ? "Members see the changes straight away." : "You become the owner and first member.",
    "actions" => component("button", ["label" => $editing ? "Back to group" : "Back to groups", "icon" => "arrow-left", "variant" => "outline", "size" => "sm", "href" => url($editing ? "/groups/" . (int) $group["id"] : "/groups"), "class" => "sc-btn-quiet"]),
]) ?>
<div class="sc-form-narrow">
    <form class="sc-panel" method="post" action="<?= e(url($editing ? "/groups/" . (int) $group["id"] . "/edit" : "/groups")) ?>">
        <?= csrf_field() ?>
        <?= component("form-field", ["name" => "name", "label" => "Group name", "value" => $value("name"), "placeholder" => "CS204 Exam Sprint Crew", "required" => true, "attrs" => ["maxlength" => 100]]) ?>
        <?php if ($editing): ?>
            <?= component("form-field", ["name" => "subject_display", "label" => "Subject", "value" => $group["subject"] . " " . $group["subject_name"], "attrs" => ["disabled" => true], "help" => "The subject cannot be changed."]) ?>
        <?php else: ?>
            <?= component("form-field", ["name" => "subject_id", "label" => "Subject", "type" => "select", "placeholder" => "Choose a subject", "options" => $subjects, "required" => true]) ?>
        <?php endif; ?>
        <?= component("form-field", ["name" => "description", "label" => "What is this group for?", "type" => "textarea", "rows" => 4, "value" => $value("description"), "required" => true, "attrs" => ["maxlength" => 500]]) ?>
        <?= component("form-field", ["name" => "max_members", "label" => "Member limit", "type" => "number", "value" => $value("max_members", "12"), "required" => true, "attrs" => ["min" => 2, "max" => 100]]) ?>
        <?= component("button", ["label" => $editing ? "Save changes" : "Create group", "type" => "submit", "variant" => "dark"]) ?>
    </form>
</div>
