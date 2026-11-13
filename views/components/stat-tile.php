<?php
/**
 * components/stat-tile.php  -  dashboard stat: caps label + trend chip, big number, one footer line
 *   <?= component('stat-tile', ['label' => 'Notes shared', 'value' => 2481, 'tag' => '+12.5%', 'down' => false,
 *         'footIcon' => 'folder2', 'foot' => 'Active peer vaults across 6 faculties']) ?>
 * footIcon 'dot' shows the lime live dot instead of an icon.
 * live     ['value' => key, 'tag' => key, 'foot' => key] for SC.live (optional)
 */
$value = $value ?? 0;
$shown = is_int($value) || is_float($value) ? number_format($value) : (string) $value;
$footIcon = $footIcon ?? "";
$down = !empty($down);
$live = $live ?? [];
?>
<article class="sc-stat-tile">
    <div class="sc-stat-tile__top">
        <span class="sc-stat-tile__label"><?= e($label ?? "") ?></span>
        <?php if (!empty($tag)): ?><span class="sc-tag sc-tag--sm<?= $down ? " sc-tag--coral" : "" ?>"<?= html_attrs(["data-live-tag" => $live["tag"] ?? null, "data-down-class" => isset($live["tag"]) ? "sc-tag--coral" : null]) ?>><?= e($tag) ?></span><?php endif; ?>
    </div>
    <p class="sc-stat-tile__value"<?= html_attrs(["data-live-value" => $live["value"] ?? null, "data-count" => is_numeric($value) ? (string) $value : null]) ?>><?= e($shown) ?></p>
    <p class="sc-stat-tile__foot">
        <?php if ($footIcon === "dot"): ?><span class="sc-dot sc-dot--sm" aria-hidden="true"></span>
        <?php elseif ($footIcon !== ""): ?><i class="bi bi-<?= e($footIcon) ?>" aria-hidden="true"></i><?php endif; ?>
        <span<?= html_attrs(["data-live-text" => $live["foot"] ?? null]) ?>><?= e($foot ?? "") ?></span>
    </p>
</article>
