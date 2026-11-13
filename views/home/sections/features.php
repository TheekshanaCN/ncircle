<?php
/** views/home/sections/features.php  -  landing page section 3: the four product areas */
$art = fn (string $file) => '<img class="sc-feature__art" src="' . e(asset("img/illustrations/" . $file . ".svg")) . '" alt="" width="64" height="64" loading="lazy">';
$notesMedia = '<div class="sc-feature__badges">'
    . '<span class="sc-feature__badge sc-feature__badge--dark">Y1</span>'
    . '<span class="sc-feature__badge sc-feature__badge--dark">Y2</span>'
    . '<span class="sc-feature__badge sc-feature__badge--lime">Y3</span>'
    . '<span class="sc-feature__badge sc-feature__badge--outline">Y4</span></div>'
    . $art("feature-notes");
?>
<section class="landing-section" id="features" aria-label="Features">
    <?= component("section-head", [
        "eyebrow" => "Academic structure",
        "title" => "Tailored to the NSBM degree pathway",
        "subtitle" => "Engineered around degree modules from introductory Year 01 lectures through to final-year capstone defense.",
    ]) ?>

    <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-3"><?= component("feature-card", [
            "media" => $notesMedia,
            "title" => "Note library (Year 1–4)",
            "text" => "Lectures, lab sheets, and past papers organized by official NSBM module codes and semester tracks.",
            "linkLabel" => "Browse module repository",
            "linkHref" => url("/resources"),
        ]) ?></div>
        <div class="col-12 col-md-6 col-lg-3"><?= component("feature-card", [
            "media" => $art("feature-forum"),
            "title" => "Academic Q&A forum",
            "text" => "Post coursework doubts and obtain peer answers cross-referenced against marking rubrics and lecturer notes.",
            "linkLabel" => "Explore syllabus forum",
            "linkHref" => url("/forum"),
        ]) ?></div>
        <div class="col-12 col-md-6 col-lg-3"><?= component("feature-card", [
            "media" => $art("feature-pods"),
            "title" => "Synchronized pods",
            "text" => "Join exam sprint rooms with timetable integration, lab reservation schedules, and focused peer review.",
            "linkLabel" => "View active pods",
            "linkHref" => url("/sessions"),
        ]) ?></div>
        <div class="col-12 col-md-6 col-lg-3"><?= component("feature-card", [
            "media" => $art("feature-vault"),
            "title" => "Personal academic vault",
            "text" => "Store private study bookmarks, upload approval logs, and monitor your NSBM honor verification standing.",
            "linkLabel" => "Open personal vault",
            "linkHref" => url("/space"),
        ]) ?></div>
    </div>
</section>
