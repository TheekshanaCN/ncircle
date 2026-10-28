<?php  ?>

<section class="mb-5 faculty-header">
    
    <?= component("section-head", [
        "eyebrow" => "FACULTY BENCHMARKS",
        "title" => "Verified academic impact at NSBM",
        "subtitle" =>
            "Directly moderated by course representatives and senior 
            high-distinction scholars across all undergraduate terms.",
        "align" => "start",
    ]) ?>
</section>

<section class="mb-5">
    
    <div class="row g-4">
        <div class="col-12 col-sm-6 col-lg-3"><?= component("stat-card", [
            "icon" => "file-earmark-text",
            "value" => 2481,
            "label" => "Notes shared",
            "tag" => "+12.5%",
            "foot" => ["8 Computing disciplines", "Active Vault"],
        ]) ?></div>
        <div class="col-12 col-sm-6 col-lg-3"><?= component("stat-card", [
            "icon" => "people",
            "value" => 1236,
            "label" => "Active students",
            "tag" => "+7.2%",
            "foot" => ["Active past 24 hours"],
        ]) ?></div>
        <div class="col-12 col-sm-6 col-lg-3"><?= component("stat-card", [
            "icon" => "chat-left",
            "value" => 843,
            "label" => "Questions answered",
            "tag" => "-2.5%",
            "foot" => ["Median response: 18m", "94% Solved"],
            "down" => true,
        ]) ?></div>
        <div class="col-12 col-sm-6 col-lg-3"><?= component("stat-card", [
            "icon" => "diagram-3",
            "value" => "64",
            "label" => "Study groups",
            "tag" => "Active",
            "foot" => ["Exam prep rooms", "Synchronized"],
        ]) ?></div>
    </div>
</section>

