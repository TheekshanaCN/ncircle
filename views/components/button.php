<?php
/**
 * components/button.php
 *
 *   <?= component('button', ['label' => 'Upload note', 'href' => url('/resources/create'), 'icon' => 'upload']) ?>
 *   <?= component('button', ['label' => 'Save', 'type' => 'submit']) ?>
 *   <?= component('button', ['label' => 'Delete', 'variant' => 'danger', 'attrs' => ['data-bs-toggle' => 'modal', 'data-bs-target' => '#confirmDelete']]) ?>
 *
 * label        text (escaped)
 * href         if set, renders <a>; otherwise <button>
 * type         button | submit   (only for <button>, default button)
 * variant      primary (lime) | outline | light (on teal) | danger | link
 * size         sm | lg | '' (default)
 * icon         Bootstrap Icons name without "bi-", e.g. 'arrow-right'
 * iconPosition start | end
 * class        extra classes   attrs  extra attributes (array)
 */
$label        = $label ?? '';
$href         = $href ?? null;
$type         = $type ?? 'button';
$variant      = $variant ?? 'primary';
$size         = $size ?? '';
$icon         = $icon ?? null;
$iconPosition = $iconPosition ?? 'start';
$class        = $class ?? '';
$attrs        = $attrs ?? [];

$variants = [
    'primary' => 'btn-primary',
    'outline' => 'btn-outline-primary',
    'light'   => 'btn-outline-light',
    'danger'  => 'btn-danger',
    'link'    => 'btn-link',
];
$classes = trim('btn ' . ($variants[$variant] ?? 'btn-primary')
    . ($size !== '' ? ' btn-' . $size : '')
    . ($class !== '' ? ' ' . $class : ''));

$iconHtml = $icon ? '<i class="bi bi-' . e($icon) . '" aria-hidden="true"></i>' : '';
$inner = $iconPosition === 'end'
    ? e($label) . ($iconHtml ? ' ' . $iconHtml : '')
    : ($iconHtml ? $iconHtml . ' ' : '') . e($label);
?>
<?php if ($href !== null): ?>
<a class="<?= e($classes) ?>" href="<?= e($href) ?>"<?= html_attrs($attrs) ?>><?= $inner ?></a>
<?php else: ?>
<button class="<?= e($classes) ?>" type="<?= e($type) ?>"<?= html_attrs($attrs) ?>><?= $inner ?></button>
<?php endif; ?>
