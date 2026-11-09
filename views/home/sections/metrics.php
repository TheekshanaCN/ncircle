<?php
/** views/home/sections/metrics.php  -  landing page section 2: real, live impact numbers */
$values = $live["values"];
$tags = $live["tags"];
$texts = $live["texts"];
?>
<section class="landing-metrics" id="community" aria-labelledby="metrics-title">
    <div class="landing-metrics__head">
        <div>
            <span class="sc-eyebrow">Faculty benchmarks</span>
            <h2 class="sc-section-head__title" id="metrics-title">Verified academic impact at NSBM</h2>
            <?= component("live-badge") ?>
        </div>
        <p>Directly moderated by course representatives and senior high-distinction scholars across all undergraduate terms.</p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-sm-6 col-lg-3"><?= component("stat-card", [
            "icon" => "file-earmark-text",
            "value" => $values["notes"],
            "label" => "Notes shared",
            "tag" => $tags["notes"]["label"],
            "down" => $tags["notes"]["down"],
            "foot" => [$texts["notesFoot"], "Active Vault"],
            "live" => ["value" => "notes", "tag" => "notes", "foot" => ["notesFoot", null]],
        ]) ?></div>
        <div class="col-12 col-sm-6 col-lg-3"><?= component("stat-card", [
            "icon" => "people",
            "value" => $values["activeStudents"],
            "label" => "Active students",
            "tag" => $tags["activeStudents"]["label"],
            "down" => $tags["activeStudents"]["down"],
            "foot" => ["Active past 24 hours"],
            "live" => ["value" => "activeStudents", "tag" => "activeStudents"],
        ]) ?></div>
        <div class="col-12 col-sm-6 col-lg-3"><?= component("stat-card", [
            "icon" => "chat-left",
            "value" => $values["answered"],
            "label" => "Questions answered",
            "tag" => $tags["answered"]["label"],
            "down" => $tags["answered"]["down"],
            "foot" => [$texts["answeredFootA"], $texts["answeredFootB"]],
            "live" => ["value" => "answered", "tag" => "answered", "foot" => ["answeredFootA", "answeredFootB"]],
        ]) ?></div>
        <div class="col-12 col-sm-6 col-lg-3"><?= component("stat-card", [
            "icon" => "clock",
            "value" => $values["groups"],
            "label" => "Study groups",
            "tag" => "Active",
            "foot" => [$texts["groupsFoot"], "Synchronized"],
            "live" => ["value" => "groups", "foot" => ["groupsFoot", null]],
        ]) ?></div>
    </div>
</section>
