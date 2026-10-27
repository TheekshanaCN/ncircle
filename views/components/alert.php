<?php
/**
 * components/alert.php  -  message box (success after upload, validation summary, warnings)
 *
 *   <?= component('alert', ['type' => 'success', 'message' => 'Uploaded. An admin will review it soon.']) ?>
 *   <?= component('alert', ['type' => 'danger', 'title' => 'Could not save', 'message' => 'The file is too large.', 'dismissible' => true]) ?>
 *
 * type  success | danger | warning | info
 */
$type        = $type ?? 'info';
$message     = $message ?? '';
$title       = $title ?? '';
$dismissible = !empty($dismissible);
$icons = [
    'success' => 'check-circle-fill',
    'danger'  => 'exclamation-octagon-fill',
    'warning' => 'exclamation-triangle-fill',
    'info'    => 'info-circle-fill',
];
$type = isset($icons[$type]) ? $type : 'info';
?>
<div class="alert alert-<?= e($type) ?> sc-alert<?= $dismissible ? ' alert-dismissible fade show' : '' ?>" role="<?= $type === 'danger' ? 'alert' : 'status' ?>">
    <i class="bi bi-<?= e($icons[$type]) ?>" aria-hidden="true"></i>
    <div>
        <?php if ($title !== ''): ?><strong class="d-block"><?= e($title) ?></strong><?php endif; ?>
        <?= e($message) ?>
    </div>
    <?php if ($dismissible): ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button><?php endif; ?>
</div>
