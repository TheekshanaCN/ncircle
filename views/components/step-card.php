<?php
/**
 *   <div class="row g-4">
 *     <div class="col-12 col-lg-4">
 *       <?= component('step-card', [
 *             'number' => 1,
 *             'icon'   => 'upload',
 *             'title'  => 'Upload your note',
 *             'text'   => 'Drop PDF, DOCX, or PPTX slides with module codes, lecture weeks, and relevant professor notes.',
 *       ]) ?>
 *     </div>
 *   </div>
 *
 * number = shown in the lime circle     icon = Bootstrap Icons name (no "bi-")
 */
$number = $number ?? 1;
$icon = $icon ?? "circle";
$title = $title ?? "";
$text = $text ?? "";
?>
<article class="sc-card sc-step">
    <span class="sc-step__number" aria-hidden="true"><?= e(
        (string) $number,
    ) ?></span>
    <span class="sc-step__icon"><i class="bi bi-<?= e(
        $icon,
    ) ?>" aria-hidden="true"></i></span>
    <h3 class="sc-step__title"><span class="visually-hidden">Step <?= e(
        (string) $number,
    ) ?>: </span><?= e($title) ?></h3>
    <p class="sc-step__text"><?= e($text) ?></p>
</article>
