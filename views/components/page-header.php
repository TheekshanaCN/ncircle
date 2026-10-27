<?php
/**
 * components/page-header.php  -  title row at the top of app/admin pages
 *
 *   <?= component('page-header', [
 *         'title'    => 'Study resources',
 *         'subtitle' => 'Notes shared by NSBM students',
 *         'actions'  => component('button', ['label' => 'Upload note', 'href' => url('/resources/create'), 'icon' => 'upload']),
 *   ]) ?>
 *
 * actions = trusted HTML (usually built with component('button')). Everything else is escaped.
 */
$title    = $title ?? '';
$subtitle = $subtitle ?? '';
$actions  = $actions ?? '';
?>
<div class="sc-page-header">
    <div>
        <h1 class="sc-page-header__title"><?= e($title) ?></h1>
        <?php if ($subtitle !== ''): ?>
            <p class="sc-page-header__subtitle"><?= e($subtitle) ?></p>
        <?php endif; ?>
    </div>
    <?php if ($actions !== ''): ?>
        <div class="d-flex flex-wrap gap-2"><?= $actions ?></div>
    <?php endif; ?>
</div>
