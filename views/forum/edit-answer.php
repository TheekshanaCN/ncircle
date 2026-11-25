<?php
/** views/forum/edit-answer.php  -  edit the text of your own answer */
$back = url("/forum/" . (int) $answer["question_id"] . "#answer-" . (int) $answer["id"]);
?>
<?= component("page-header", [
    "title" => "Edit answer",
    "subtitle" => "Images and files you attached stay as they are.",
    "actions" => component("button", ["label" => "Back to question", "icon" => "arrow-left", "variant" => "outline", "size" => "sm", "href" => $back, "class" => "sc-btn-quiet"]),
]) ?>
<div class="sc-form-narrow">
    <form class="sc-panel" method="post" action="<?= e(url("/answers/" . (int) $answer["id"] . "/edit")) ?>">
        <?= csrf_field() ?>
        <?= component("form-field", ["name" => "body", "label" => "Answer", "type" => "textarea", "rows" => 7, "value" => old("body", $answer["body"]), "attrs" => ["maxlength" => 5000]]) ?>
        <?= component("button", ["label" => "Save changes", "type" => "submit", "variant" => "dark"]) ?>
    </form>
</div>
