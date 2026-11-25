<?php
/**
 * components/stacked-bars.php  -  interactive stacked column chart (no chart library)
 *   <?= component('stacked-bars', ['height' => 150, 'liveKey' => 'weekly', 'median' => 42, 'medianValue' => 3,
 *         'series' => ['Notes shared', 'Answers given'], 'items' => [
 *           ['label' => 'Mon', 'detail' => 'Mon, Nov 2', 'a' => 3, 'b' => 4, 'top' => 30, 'bottom' => 40, 'active' => false, 'tooltip' => '', 'title' => 'Mon: 3 notes, 4 answers'],
 *   ]]) ?>
 * top, bottom and median are percentages of the plot height. a and b are the real numbers behind them.
 * top/a = hatched lime segment, bottom/b = teal segment.
 * Hover, tap or focus a bar for its numbers. Arrow keys move between bars. Pair it with a legend:
 *   <ul class="sc-legend" data-legend-for="weekly"><li><button class="sc-legend__item" data-legend="top" aria-pressed="true">...</button></li></ul>
 * liveKey (optional) lets SC.live update the bars in place, so they animate instead of being redrawn.
 */
$items = $items ?? [];
$height = (int) ($height ?? 150);
$median = $median ?? null;
$medianValue = $medianValue ?? null;
$medianLabel = $medianLabel ?? "";
$liveKey = $liveKey ?? null;
$series = $series ?? ["", ""];
$lastIndex = count($items) - 1;
?>
<div class="sc-bars" style="--sc-bars-h: <?= $height ?>px"<?= html_attrs(["data-live-bars" => $liveKey, "data-series-a" => $series[0], "data-series-b" => $series[1], "data-median-value" => $medianValue !== null ? (string) $medianValue : null]) ?>>
    <div class="sc-bars__plot">
        <?php if ($median !== null): ?>
            <span class="sc-bars__median" style="bottom: <?= round((float) $median, 1) ?>%"><em<?= $medianLabel === "" ? " hidden" : "" ?>><?= e($medianLabel) ?></em></span>
        <?php endif; ?>
        <?php foreach ($items as $index => $item):
            $top = (float) ($item["top"] ?? 0);
            $bottom = (float) ($item["bottom"] ?? 0);
            $total = $top + $bottom;
            // Keep the tooltip inside the chart when the peak is near an edge.
            $tipEdge = $index <= 1 ? " sc-chart-tip--start" : ($index >= $lastIndex - 1 ? " sc-chart-tip--end" : "");
        ?>
            <div class="sc-bars__col<?= !empty($item["active"]) ? " is-active" : "" ?>" style="--i: <?= (int) $index ?>" tabindex="0" role="img"<?= html_attrs(["aria-label" => $item["title"] ?? null, "data-a" => (string) ($item["a"] ?? 0), "data-b" => (string) ($item["b"] ?? 0), "data-detail" => $item["detail"] ?? ($item["label"] ?? "")]) ?>>
                <div class="sc-bars__stack" style="height: <?= round($total, 1) ?>%">
                    <?php if (!empty($item["tooltip"])): ?>
                        <span class="sc-chart-tip<?= $tipEdge ?>"><span class="sc-dot sc-dot--xs" aria-hidden="true"></span><span><?= e($item["tooltip"]) ?></span></span>
                    <?php endif; ?>
                    <div class="sc-bars__segments">
                        <span class="sc-bars__top" style="height: <?= $total > 0 ? round($top / $total * 100, 1) : 0 ?>%"></span>
                        <span class="sc-bars__bottom" style="height: <?= $total > 0 ? round($bottom / $total * 100, 1) : 0 ?>%"></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="sc-bars__labels" aria-hidden="true">
        <?php foreach ($items as $item): ?>
            <span class="<?= !empty($item["active"]) ? "is-active" : "" ?>"><?= e($item["label"] ?? "") ?></span>
        <?php endforeach; ?>
    </div>
</div>
