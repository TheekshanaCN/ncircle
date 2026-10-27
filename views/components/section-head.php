<?php
/**
 * components/section-head.php  -  lime pill + big title + subtitle (landing sections)
 *
 *   <?= component('section-head', [
 *         'eyebrow'  => 'Academic structure',
 *         'title'    => 'Tailored to the NSBM degree pathway',
 *         'subtitle' => 'Engineered around degree modules from Year 01 to final-year capstone defense.',
 *   ]) ?>
 *
 * align = 'center' (default) | 'start'
 */
$eyebrow  = $eyebrow ?? '';
$title    = $title ?? '';
$subtitle = $subtitle ?? '';
$align    = $align ?? 'center';
?>
<div class="sc-section-head<?= $align === 'start' ? ' sc-section-head--start' : '' ?>">
    <?php if ($eyebrow !== ''): ?><span class="sc-eyebrow"><?= e($eyebrow) ?></span><?php endif; ?>
    <h2 class="sc-section-head__title"><?= e($title) ?></h2>
    <?php if ($subtitle !== ''): ?><p class="sc-section-head__sub"><?= e($subtitle) ?></p><?php endif; ?>
</div>
