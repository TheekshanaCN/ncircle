<?php
/** views/dashboard/index.php  -  student dashboard (Figma frame "Dashboard") */
$hour = (int) date("G");
$greeting = $hour < 12 ? "Good Morning" : ($hour < 17 ? "Good Afternoon" : "Good Evening");
$firstName = strtok($user["name"], " ");
$goal = $live["goal"];
$values = $live["values"];
$tags = $live["tags"];
$texts = $live["texts"];
$pace = $goal["pace"];
$paceText = $pace === 0
    ? "You're matching last week's pace."
    : ($pace > 0 ? "You're " . format_minutes($pace) . " ahead of last week's pace." : "You're " . format_minutes(-$pace) . " behind last week's pace.");
?>
<div data-live-root data-live-url="<?= e(url("/api/stats/dashboard")) ?>">
<section class="dash-hero" aria-labelledby="dash-greeting" data-spotlight>
    <div class="dash-hero__main">
        <span class="dash-hero__live"><span class="sc-dot sc-dot--sm sc-dot--pulse" aria-hidden="true"></span><span><span data-live-value="online"><?= number_format($values["online"]) ?></span> <span data-live-text="onlineWord"><?= e($texts["onlineWord"]) ?></span> studying online &bull; <span data-live-value="streak"><?= (int) $goal["streak"] ?></span> <span data-live-text="streakWord"><?= e($texts["streakWord"]) ?></span> streak</span></span>
        <h1 class="dash-hero__title" id="dash-greeting"><?= e($greeting) ?>, <?= e($firstName) ?>! Welcome back to your <span class="sc-script">Ncircle.</span></h1>
        <p class="dash-hero__sub">Here's what's happening in your circle today. <?= e($paceText) ?></p>
        <div class="dash-hero__actions">
            <?= component("button", ["label" => "Upload note", "icon" => "plus-lg", "size" => "sm", "href" => url("/resources/create")]) ?>
            <?= component("button", ["label" => "Ask question", "icon" => "chat-square", "variant" => "light", "size" => "sm", "href" => url("/forum/create")]) ?>
            <?= component("button", ["label" => "Join a meeting", "icon" => "people-fill", "variant" => "light", "size" => "sm", "href" => url("/sessions")]) ?>
        </div>
        <div class="dash-goal">
            <div class="dash-goal__row">
                <span><i class="bi bi-graph-up-arrow" aria-hidden="true"></i> Weekly Study Goal: <span data-live-value="goalPercent"><?= (int) $goal["percent"] ?></span>% complete</span>
                <span class="dash-goal__muted" data-live-text="goalHours"><?= e($texts["goalHours"]) ?></span>
            </div>
            <div class="sc-progress" role="progressbar" aria-valuenow="<?= (int) $goal["percent"] ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Weekly study goal"><span data-live-width="goal" style="width: <?= (int) $goal["percent"] ?>%"></span></div>
        </div>
    </div>
    <div class="dash-hero__card">
        <img src="<?= e(asset("img/illustrations/dashboard-student.svg")) ?>" width="246" height="112" alt="">
        <strong>Year <?= (int) $user["academic_year"] ?> Peer Circle</strong>
        <span><?= e(config("app.faculties")[$user["faculty"]] ?? $user["faculty"]) ?></span>
    </div>
</section>

<section class="dash-stats" aria-label="Your faculty's numbers">
    <?= component("stat-tile", ["label" => "Notes shared", "value" => $values["notes"], "tag" => $tags["notes"]["label"], "down" => $tags["notes"]["down"], "footIcon" => "folder2", "foot" => $texts["notesFoot"], "live" => ["value" => "notes", "tag" => "notes", "foot" => "notesFoot"]]) ?>
    <?= component("stat-tile", ["label" => "Active students", "value" => $values["students"], "tag" => $tags["students"]["label"], "down" => $tags["students"]["down"], "footIcon" => "dot", "foot" => $texts["studentsFoot"], "live" => ["value" => "students", "tag" => "students", "foot" => "studentsFoot"]]) ?>
    <?= component("stat-tile", ["label" => "Questions answered", "value" => $values["answered"], "tag" => $tags["answered"]["label"], "down" => $tags["answered"]["down"], "footIcon" => "patch-check", "foot" => $texts["answeredFoot"], "live" => ["value" => "answered", "tag" => "answered", "foot" => "answeredFoot"]]) ?>
</section>

<section class="sc-panel" aria-labelledby="velocity-title">
    <div class="dash-chart-head">
        <div>
            <h2 class="sc-panel__title" id="velocity-title">Peer Learning Velocity &amp; Cohort Benchmark <span class="sc-state sc-state--dark" data-live-text="chartBadge"><?= e($texts["chartBadge"]) ?></span> <?= component("live-badge") ?></h2>
            <p class="sc-panel__sub">Stacked contributions: Notes uploaded vs verified forum answers compared to cohort median.</p>
        </div>
        <div class="dash-chart-tools">
            <div class="sc-range" role="group" aria-label="Chart time range">
                <button type="button" class="sc-range__btn is-on" data-range="week" aria-pressed="true">7 days</button>
                <button type="button" class="sc-range__btn" data-range="month" aria-pressed="false">4 weeks</button>
                <button type="button" class="sc-range__btn" data-range="half" aria-pressed="false">6 months</button>
            </div>
            <ul class="sc-legend" data-legend-for="weekly">
                <li><button type="button" class="sc-legend__item" data-legend="top" aria-pressed="true"><span class="sc-legend__swatch sc-legend__swatch--hatch"></span>Notes Shared</button></li>
                <li><button type="button" class="sc-legend__item" data-legend="bottom" aria-pressed="true"><span class="sc-legend__swatch sc-legend__swatch--teal"></span>Answers Given</button></li>
                <li><button type="button" class="sc-legend__item" data-legend="median" aria-pressed="true"><span class="sc-legend__swatch sc-legend__swatch--line"></span>Cohort Median</button></li>
            </ul>
        </div>
    </div>
    <?= component("stacked-bars", array_merge($live["bars"]["weekly"], ["height" => 170, "liveKey" => "weekly"])) ?>
    <?php if ($mastery): ?>
        <div class="dash-mastery">
            <span class="dash-mastery__label">Subject mastery:</span>
            <?php foreach ($mastery as $m): ?>
                <a class="dash-mastery__pill" href="<?= e(url("/resources?subject[]=" . (int) $m["id"])) ?>"><?= e($m["name"]) ?> (<?= e($m["code"]) ?>) <b><?= (int) $m["score"] ?>%</b></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

</div>

<div class="dash-split">
    <section class="sc-panel sc-panel--tight" aria-labelledby="top-notes">
        <header class="sc-panel__head">
            <h2 class="sc-panel__title" id="top-notes"><i class="bi bi-star" aria-hidden="true"></i> Top-Rated Notes</h2>
        </header>
        <?php if ($topNotes): ?>
            <div class="sc-stack">
                <?php foreach ($topNotes as $note): ?><?= component("note-row", ["resource" => $note]) ?><?php endforeach; ?>
            </div>
        <?php else: ?>
            <?= component("empty-state", ["icon" => "folder2-open", "title" => "No notes yet", "text" => "Be the first to share one.", "actionLabel" => "Share a note", "actionHref" => url("/resources/create")]) ?>
        <?php endif; ?>
    </section>

    <section class="sc-panel sc-panel--tight" aria-labelledby="recent-threads">
        <header class="sc-panel__head">
            <h2 class="sc-panel__title" id="recent-threads"><i class="bi bi-chat-left-text" aria-hidden="true"></i> Recent Discussions</h2>
            <a class="sc-panel__link" href="<?= e(url("/forum")) ?>">Enter Discussion forum <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </header>
        <?php if ($threads): ?>
            <div class="sc-stack">
                <?php foreach ($threads as $thread): ?><?= component("thread-card", ["question" => $thread]) ?><?php endforeach; ?>
            </div>
        <?php else: ?>
            <?= component("empty-state", ["icon" => "chat-left-text", "title" => "No questions yet", "text" => "Start the first discussion.", "actionLabel" => "Ask question", "actionHref" => url("/forum/create")]) ?>
        <?php endif; ?>
    </section>
</div>

<section class="sc-panel" aria-labelledby="upcoming-pods">
    <header class="sc-panel__head">
        <div>
            <h2 class="sc-panel__title" id="upcoming-pods"><span class="sc-dot sc-dot--sm" aria-hidden="true"></span> Upcoming Meetings</h2>
            <p class="sc-panel__sub">Online, physical and hybrid study meetings in your faculty.</p>
        </div>
        <?= component("button", ["label" => "Schedule meeting", "icon" => "plus-circle", "variant" => "outline", "size" => "sm", "href" => url("/sessions/create"), "class" => "sc-btn-quiet"]) ?>
    </header>
    <?php if ($sessions): ?>
        <div class="dash-pods">
            <?php foreach ($sessions as $session): ?><?= component("pod-card", ["session" => $session]) ?><?php endforeach; ?>
        </div>
    <?php else: ?>
        <?= component("empty-state", ["icon" => "calendar-event", "title" => "No meetings scheduled", "text" => "Start one and invite your batch.", "actionLabel" => "Schedule meeting", "actionHref" => url("/sessions/create")]) ?>
    <?php endif; ?>
</section>
