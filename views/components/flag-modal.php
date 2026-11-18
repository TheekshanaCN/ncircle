<?php
/**
 * components/flag-modal.php  -  "Report this" dialog. Print once per page, open from any button:
 *   <button data-bs-toggle="modal" data-bs-target="#flagModal" data-flag-type="answer" data-flag-id="12">Report</button>
 */
?>
<div class="modal fade" id="flagModal" tabindex="-1" aria-labelledby="flagModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= e(url("/flag")) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="target_type" data-flag-type-input>
                <input type="hidden" name="target_id" data-flag-id-input>
                <div class="modal-header">
                    <h2 class="modal-title h5" id="flagModalTitle">Report this content</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= component("form-field", ["name" => "reason", "label" => "What is wrong with it?", "type" => "textarea", "rows" => 3, "required" => true, "error" => "", "value" => "", "attrs" => ["maxlength" => 255, "minlength" => 5]]) ?>
                </div>
                <div class="modal-footer">
                    <?= component("button", ["label" => "Cancel", "variant" => "outline", "size" => "sm", "attrs" => ["data-bs-dismiss" => "modal"]]) ?>
                    <?= component("button", ["label" => "Send report", "variant" => "dark", "size" => "sm", "type" => "submit"]) ?>
                </div>
            </form>
        </div>
    </div>
</div>
