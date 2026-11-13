<?php /** views/resources/create.php  -  upload a note */ ?>
<?= component("page-header", [
    "title" => "Share a note",
    "subtitle" => "An admin reviews every upload before it goes live for the campus circle.",
    "actions" => component("button", ["label" => "Back to notes", "icon" => "arrow-left", "variant" => "outline", "size" => "sm", "href" => url("/resources"), "class" => "sc-btn-quiet"]),
]) ?>

<div class="sc-form-layout">
    <form class="sc-panel" method="post" action="<?= e(url("/resources")) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?= component("form-field", ["name" => "title", "label" => "Title", "placeholder" => "Normalization Cheat Sheet — Database Systems", "required" => true, "attrs" => ["maxlength" => 160]]) ?>
        <div class="row g-3">
            <div class="col-md-6"><?= component("form-field", ["name" => "subject_id", "label" => "Subject", "type" => "select", "placeholder" => "Choose a subject", "options" => $subjects, "required" => true]) ?></div>
            <div class="col-md-6"><?= component("form-field", ["name" => "category_id", "label" => "Category", "type" => "select", "placeholder" => "Choose a category", "options" => $categories, "required" => true]) ?></div>
        </div>
        <?= component("form-field", ["name" => "description", "label" => "Description", "type" => "textarea", "rows" => 4, "placeholder" => "What is in this note? Mention lecture weeks and topics.", "required" => true, "attrs" => ["maxlength" => 2000]]) ?>

        <div class="sc-field">
            <label class="form-label" for="field-file">File <span class="sc-field__req" aria-hidden="true">*</span></label>
            <label class="sc-dropzone d-block" data-dropzone>
                <i class="bi bi-cloud-arrow-up sc-dropzone__icon" aria-hidden="true"></i>
                <span class="d-block" data-dropzone-label>Drop your file here, or click to choose</span>
                <small>PDF, DOCX or PPTX, up to 10 MB</small>
                <input class="visually-hidden" id="field-file" type="file" name="file" accept=".pdf,.docx,.pptx" required<?= field_error("file") !== "" ? ' aria-describedby="field-file-error" aria-invalid="true"' : "" ?>>
            </label>
            <?php if (field_error("file") !== ""): ?><div class="sc-field__error" id="field-file-error"><i class="bi bi-exclamation-circle" aria-hidden="true"></i> <?= e(field_error("file")) ?></div><?php endif; ?>
        </div>

        <div class="row g-3">
            <div class="col-md-6"><?= component("form-field", ["name" => "pages", "label" => "Pages (optional)", "type" => "number", "attrs" => ["min" => 1, "max" => 5000]]) ?></div>
        </div>
        <?= component("button", ["label" => "Submit for review", "type" => "submit", "variant" => "dark", "icon" => "upload"]) ?>
    </form>

    <aside class="sc-panel sc-panel--tight sc-side-note">
        <h2 class="sc-panel__title"><i class="bi bi-shield-check" aria-hidden="true"></i> Before you share</h2>
        <ul>
            <li>Share your own work, or material you have the right to share.</li>
            <li>Use the correct module code so peers can find it.</li>
            <li>No answers to exams or assignments that are still open.</li>
        </ul>
        <a class="sc-panel__link" href="<?= e(url("/integrity")) ?>">Read the Honor Code <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </aside>
</div>
