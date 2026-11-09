<?php
/**
 * views/home/sections/dashboard-preview.php  -  landing page section 4: real activity across all faculties
 */
$monthly = $live["bars"]["monthly"];
$heat = $live["heat"];
$bubbles = $live["bubbles"];
$bubbleStyles = ["teal", "lime", "pale"];
?>
<section class="landing-section" id="statistics" aria-label="Study activity preview">
    <?= component("section-head", [
        "eyebrow" => "Visual intelligence",
        "title" => "See your study activity at a glance",
        "subtitle" => "Live numbers showing note uploads, peak revision sprints, and topic engagement.",
    ]) ?>

    <div class="landing-preview">
        <article class="landing-preview__chart">
            <div class="landing-preview__chart-head">
                <div>
                    <h3>Notes shared per month</h3>
                    <p>Last 10 months</p>
                </div>
                <ul class="sc-legend" data-legend-for="monthly">
                    <li><button type="button" class="sc-legend__item" data-legend="top" aria-pressed="true"><span class="sc-legend__swatch sc-legend__swatch--hatch"></span>Verified Notes</button></li>
                    <li><button type="button" class="sc-legend__item" data-legend="bottom" aria-pressed="true"><span class="sc-legend__swatch sc-legend__swatch--teal"></span>Under Review</button></li>
                </ul>
            </div>
            <?= component("stacked-bars", array_merge($monthly, ["height" => 250, "liveKey" => "monthly"])) ?>
            <div class="landing-preview__chart-foot">
                <span>Average acceptance: <strong data-live-text="acceptance"><?= e($live["texts"]["acceptance"]) ?></strong></span>
                <?= component("live-badge") ?>
            </div>
        </article>

        <div class="landing-preview__side">
            <article class="landing-preview__card">
                <div class="landing-preview__heat-head">
                    <h3>Weekly study heatmap</h3>
                    <span class="sc-chart-tip sc-chart-tip--static"><span class="sc-dot sc-dot--xs" aria-hidden="true"></span><span data-live-heat-peak><?= e($heat["peak"]) ?></span></span>
                </div>
                <div class="sc-heat" role="img" aria-label="Study activity per day over the last four weeks" data-live-heat>
                    <?php foreach (["M", "T", "W", "T", "F", "S", "S"] as $day): ?><span class="sc-heat__day"><?= $day ?></span><?php endforeach; ?>
                    <?php foreach ($heat["cells"] as $cell): ?><span class="sc-heat__cell sc-heat__cell--<?= (int) $cell["level"] ?>" title="<?= e($cell["title"]) ?>"></span><?php endforeach; ?>
                </div>
                <div class="sc-heat__legend">
                    <span>Less active</span>
                    <span class="sc-heat__scale" aria-hidden="true"><i class="sc-heat__cell--0"></i><i class="sc-heat__cell--1"></i><i class="sc-heat__cell--2"></i><i class="sc-heat__cell--3"></i></span>
                    <span>Peak sprint</span>
                </div>
            </article>

            <article class="landing-preview__card">
                <h3>Top subjects by notes</h3>
                <div class="sc-bubbles">
                    <?php foreach ($bubbleStyles as $i => $style): $item = $bubbles["items"][$i] ?? null; ?>
                        <div class="sc-bubble sc-bubble--<?= $style ?>" data-live-bubble="<?= $i ?>"<?= $item === null ? " hidden" : "" ?>><strong><?= (int) ($item["percent"] ?? 0) ?>%</strong><span><?= e($item["name"] ?? "") ?></span></div>
                    <?php endforeach; ?>
                </div>
                <p class="landing-preview__note" data-live-text="bubbleNote"><?= e($bubbles["note"]) ?></p>
            </article>
        </div>
    </div>
</section>
