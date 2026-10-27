<?php
/**
 * components/confirm-modal.php  -  "Are you sure?" dialog that POSTs a form
 *
 * 1) Print the modal once on the page:
 *   <?= component('confirm-modal', [
 *         'id'           => 'deleteResource',
 *         'title'        => 'Delete this note?',
 *         'message'      => 'This cannot be undone.',
 *         'action'       => url('/resources/' . $id . '/delete'),
 *         'confirmLabel' => 'Delete',
 *         'variant'      => 'danger',
 *         'fields'       => ['id' => $id],        // extra hidden inputs (optional)
 *   ]) ?>
 *
 * 2) Open it from any button:
 *   <?= component('button', ['label' => 'Delete', 'variant' => 'danger', 'attrs' => ['data-bs-toggle' => 'modal', 'data-bs-target' => '#deleteResource']]) ?>
 *
 * A CSRF input is added automatically once the csrf_field() helper exists.
 */
$id           = $id ?? 'confirmModal';
$title        = $title ?? 'Are you sure?';
$message      = $message ?? '';
$action       = $action ?? '';
$confirmLabel = $confirmLabel ?? 'Confirm';
$variant      = $variant ?? 'danger';
$fields       = $fields ?? [];
?>
<div class="modal fade" id="<?= e($id) ?>" tabindex="-1" aria-labelledby="<?= e($id) ?>Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= e($action) ?>">
                <?= function_exists('csrf_field') ? csrf_field() : '' ?>
                <?php foreach ($fields as $fieldName => $fieldValue): ?>
                    <input type="hidden" name="<?= e((string) $fieldName) ?>" value="<?= e((string) $fieldValue) ?>">
                <?php endforeach; ?>
                <div class="modal-header">
                    <h2 class="modal-title h5" id="<?= e($id) ?>Title"><?= e($title) ?></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php if ($message !== ''): ?>
                    <div class="modal-body"><p class="mb-0"><?= e($message) ?></p></div>
                <?php endif; ?>
                <div class="modal-footer">
                    <?= component('button', ['label' => 'Cancel', 'variant' => 'outline', 'size' => 'sm', 'attrs' => ['data-bs-dismiss' => 'modal']]) ?>
                    <?= component('button', ['label' => $confirmLabel, 'variant' => $variant, 'size' => 'sm', 'type' => 'submit']) ?>
                </div>
            </form>
        </div>
    </div>
</div>
