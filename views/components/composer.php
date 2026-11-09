<?php
/**
 * components/composer.php  -  message box with emoji, GIF and file buttons. The form needs enctype="multipart/form-data".
 *   <?= component('composer', ['name' => 'body', 'label' => 'Answer', 'rows' => 5, 'maxlength' => 5000, 'required' => false]) ?>
 * Sends: the text, attachments[] and gif_id. Files are checked again on the server by FileVerifier.
 */
$GLOBALS["sc_needs_composer"] = true;
$name = $name ?? "body";
$c = config("upload.attachments");
$accept = "." . implode(",.", array_merge(array_keys($c["images"]), array_keys($c["files"])));
$fieldId = $id ?? "field-" . $name;
?>
<div class="sc-composer" data-composer data-max-files="<?= (int) $c["max_files"] ?>" data-image-max="<?= (int) $c["image_max"] ?>" data-file-max="<?= (int) $c["file_max"] ?>" data-accept="<?= e($accept) ?>" data-gif-url="<?= e(url("/api/gifs")) ?>">
    <?= component("form-field", ["id" => $fieldId, "name" => $name, "label" => $label ?? "Message", "type" => "textarea", "rows" => $rows ?? 4, "placeholder" => $placeholder ?? "", "required" => !empty($required), "attrs" => ["maxlength" => $maxlength ?? 5000, "data-composer-text" => true]]) ?>

    <div class="sc-composer__previews" data-composer-previews></div>
    <input type="hidden" name="gif_id" value="" data-composer-gif>
    <input class="visually-hidden" type="file" name="attachments[]" multiple accept="<?= e($accept) ?>" tabindex="-1" aria-label="Attach images or files" data-composer-files>

    <div class="sc-composer__bar">
        <button type="button" class="sc-composer__tool" data-composer-open="emoji" aria-expanded="false" aria-label="Add an emoji"><i class="bi bi-emoji-smile" aria-hidden="true"></i></button>
        <?php if (GiphyService::enabled()): ?>
            <button type="button" class="sc-composer__tool sc-composer__tool--gif" data-composer-open="gif" aria-expanded="false" aria-label="Add a GIF">GIF</button>
        <?php endif; ?>
        <button type="button" class="sc-composer__tool" data-composer-attach aria-label="Attach images or files"><i class="bi bi-paperclip" aria-hidden="true"></i></button>
        <span class="sc-composer__hint">Images up to <?= e(format_bytes($c["image_max"])) ?>. PDF, DOCX, PPTX, XLSX or TXT up to <?= e(format_bytes($c["file_max"])) ?>.</span>
    </div>
    <div class="sc-field__error" data-composer-error<?= field_error("attachments") === "" ? " hidden" : "" ?>><i class="bi bi-exclamation-circle" aria-hidden="true"></i> <span><?= e(field_error("attachments")) ?></span></div>

    <div class="sc-composer__panel" data-composer-panel="emoji" hidden>
        <input class="form-control form-control-sm" type="search" placeholder="Search emoji" aria-label="Search emoji" data-emoji-search>
        <div class="sc-emoji-grid" data-emoji-grid></div>
    </div>
    <?php if (GiphyService::enabled()): ?>
        <div class="sc-composer__panel" data-composer-panel="gif" hidden>
            <input class="form-control form-control-sm" type="search" placeholder="Search GIFs" aria-label="Search GIFs" data-gif-search>
            <div class="sc-gif-grid" data-gif-grid></div>
            <p class="sc-composer__credit">Powered by GIPHY</p>
        </div>
    <?php endif; ?>
</div>
