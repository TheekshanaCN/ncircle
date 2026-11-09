<?php
/**
 * components/stat-card.php  -  number + label card (landing metrics, admin dashboard)
 *
 *   <?= component('stat-card', [
 *         'icon'  => 'file-earmark-text',
 *         'value' => 2481,                 // int is formatted as 2,481
 *         'label' => 'Notes shared',
 *         'tag'   => '+12.5%',             // optional lime chip
 *         'down'  => false,                // true = red chip
 *         'foot'  => ['8 Computing disciplines', 'Active Vault'],   // optional [left, right]
 *         'live'  => ['value' => 'notes', 'tag' => 'notes', 'foot' => ['notesFoot', null]],  // optional keys for SC.live
 *   ]) ?>
 */
$icon  = $icon ?? 'bar-chart';
$value = $value ?? 0;
$label = $label ?? '';
$tag   = $tag ?? '';
$down  = !empty($down);
$foot  = $foot ?? [];
$live  = $live ?? [];
$liveFoot = $live['foot'] ?? [null, null];
$shown = is_int($value) || is_float($value) ? number_format($value) : (string) $value;
?>
<article class="sc-card sc-card--outlined sc-stat">
    <div class="sc-stat__top">
        <span class="sc-icon-circle"><i class="bi bi-<?= e($icon) ?>" aria-hidden="true"></i></span>
        <?php if ($tag !== ''): ?>
            <span class="sc-tag<?= $down ? ' sc-tag--down' : '' ?>"<?= html_attrs(['data-live-tag' => $live['tag'] ?? null, 'data-down-class' => isset($live['tag']) ? 'sc-tag--down' : null]) ?>><?= e($tag) ?></span>
        <?php endif; ?>
    </div>
    <p class="sc-stat__value"<?= html_attrs(['data-live-value' => $live['value'] ?? null, 'data-count' => is_numeric($value) ? (string) $value : null]) ?>><?= e($shown) ?></p>
    <p class="sc-stat__label"><?= e($label) ?></p>
    <?php if ($foot): ?>
        <div class="sc-stat__foot">
            <span<?= html_attrs(['data-live-text' => $liveFoot[0] ?? null]) ?>><?= e($foot[0] ?? '') ?></span>
            <?php if (!empty($foot[1])): ?><strong<?= html_attrs(['data-live-text' => $liveFoot[1] ?? null]) ?>><?= e($foot[1]) ?></strong><?php endif; ?>
        </div>
    <?php endif; ?>
</article>
