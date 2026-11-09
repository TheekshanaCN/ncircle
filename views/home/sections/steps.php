<?php /** views/home/sections/steps.php  -  landing page section 5: how moderation works */ ?>
<section class="landing-section landing-steps" id="how-it-works" aria-label="How it works">
    <?= component("section-head", [
        "eyebrow" => "Academic integrity",
        "title" => "The 3-step honor code flow",
        "subtitle" => "Every shared resource undergoes peer and batch rep scrutiny before publishing to the campus circle.",
    ]) ?>

    <div class="row g-4">
        <div class="col-12 col-lg-4"><?= component("step-card", [
            "number" => 1,
            "icon" => "upload",
            "title" => "Upload your note",
            "text" => "Drop PDF, DOCX, or PPTX slides with module codes, lecture weeks, and relevant professor notes.",
        ]) ?></div>
        <div class="col-12 col-lg-4"><?= component("step-card", [
            "number" => 2,
            "icon" => "shield-check",
            "title" => "Peer & batch rep review",
            "text" => "Elected module representatives check formatting, academic ethics, citation accuracy, and legibility.",
        ]) ?></div>
        <div class="col-12 col-lg-4"><?= component("step-card", [
            "number" => 3,
            "icon" => "people",
            "title" => "Knowledge exchange",
            "text" => "Peers download your material, ask contextual questions, and award star contributor badges.",
        ]) ?></div>
    </div>
</section>
