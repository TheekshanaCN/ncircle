<?php
/**
 * components/empty-state.php  -  "nothing here yet" block for lists
 *
 *   <?= component('empty-state', [
 *         'icon'        => 'folder2-open',
 *         'title'       => 'No notes yet',
 *         'text'        => 'Be the first to share a note for this subject.',
 *         'actionLabel' => 'Upload a note',
 *         'actionHref'  => url('/resources/create'),
 *   ]) ?>
 */
$icon        = $icon ?? 'inbox';
$title       = $title ?? 'Nothing here yet';
$text        = $text ?? '';
$actionLabel = $actionLabel ?? '';
$actionHref  = $actionHref ?? '';
?>
<div class="sc-empty">
    <div class="sc-empty__icon"><i class="bi bi-<?= e($icon) ?>" aria-hidden="true"></i></div>
    <h2 class="sc-empty__title"><?= e($title) ?></h2>
    <?php if ($text !== ''): ?><p class="sc-empty__text"><?= e($text) ?></p><?php endif; ?>
    <?php if ($actionLabel !== '' && $actionHref !== ''): ?>
        <?= component('button', ['label' => $actionLabel, 'href' => $actionHref, 'size' => 'sm']) ?>
    <?php endif; ?>
</div>
