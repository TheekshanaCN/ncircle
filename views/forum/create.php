<?php
/** views/forum/create.php  -  ask or edit a question. $question is null when asking. */
$editing = $question !== null;
$value = fn (string $key) => old($key, $editing ? $question[$key] : "");
?>
<?= component("page-header", [
    "title" => $editing ? "Edit question" : "Ask a question",
    "subtitle" => $editing ? "Images and files you attached stay as they are." : "Be specific, and include what you already tried.",
    "actions" => component("button", ["label" => $editing ? "Back to question" : "Back to forum", "icon" => "arrow-left", "variant" => "outline", "size" => "sm", "href" => url($editing ? "/forum/" . (int) $question["id"] : "/forum"), "class" => "sc-btn-quiet"]),
]) ?>
<div class="sc-form-layout">
    <form class="sc-panel" method="post" action="<?= e(url($editing ? "/forum/" . (int) $question["id"] . "/edit" : "/forum")) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?= component("form-field", ["name" => "title", "label" => "Question", "value" => $value("title"), "placeholder" => "What is the difference between 2NF and 3NF?", "required" => true, "attrs" => ["maxlength" => 200]]) ?>
        <?= component("form-field", ["name" => "subject_id", "label" => "Subject", "type" => "select", "placeholder" => "Choose a subject", "options" => $subjects, "value" => $value("subject_id"), "required" => true]) ?>
        <?php if ($editing): ?>
            <?= component("form-field", ["name" => "body", "label" => "Details", "type" => "textarea", "rows" => 7, "value" => $value("body"), "required" => true, "attrs" => ["maxlength" => 5000]]) ?>
        <?php else: ?>
            <?= component("composer", ["name" => "body", "label" => "Details", "rows" => 7, "placeholder" => "Share the context, the exam question, or your working so far.", "required" => true, "maxlength" => 5000]) ?>
        <?php endif; ?>
        <?= component("button", ["label" => $editing ? "Save changes" : "Post question", "type" => "submit", "variant" => "dark", "icon" => "send"]) ?>
    </form>
    <aside class="sc-panel sc-panel--tight sc-side-note">
        <h2 class="sc-panel__title"><i class="bi bi-lightbulb" aria-hidden="true"></i> Getting a good answer</h2>
        <ul>
            <li>Use a title that states the actual question.</li>
            <li>Pick the right module so the right peers see it.</li>
            <li>Never post questions from an exam that is still running.</li>
        </ul>
    </aside>
</div>
