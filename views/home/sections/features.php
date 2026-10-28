<?php  ?>
<section class="mb-5">
        <?= component("section-head", [
        "eyebrow" => "Academic structure",
        "title" => "Tailored to the NSBM degree pathway",
        "subtitle" =>
            "Engineered around degree modules from Year 01 lectures through to final-year capstone defense.",
    ]) ?>
</section>

<section class="mb-5">
        <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-3"><?= component("feature-card", [
            "icon" => "file-text-fill",
            "badges" => [
                ["Y1", "dark"],
                ["Y2", "dark"],
                ["Y3", "lime"],
                ["Y4", "outline"],
            ],
            "title" => "Note library (Year 1–4)",
            "text" =>
                "Lectures, lab sheets, and past papers organized by official NSBM module codes and semester tracks.",
            "linkLabel" => "Browse module repository",
            "linkHref" => "#",
        ]) ?></div>
        <div class="col-12 col-md-6 col-lg-3"><?= component("feature-card", [
            "icon" => "chat-left-dots-fill",
            "title" => "Academic Q&A forum",
            "text" =>
                "Post coursework doubts and obtain peer answers cross-referenced against marking rubrics and lecturer notes.",
            "linkLabel" => "Explore syllabus forum",
            "linkHref" => "#",
        ]) ?></div>
        <div class="col-12 col-md-6 col-lg-3"><?= component("feature-card", [
            "icon" => "people",
            "title" => "Synchronized pods",
            "text" =>
                "Join exam sprint rooms with timetable integration, lab reservation schedules, and focused peer review.",
            "linkLabel" => "View active pods",
            "linkHref" => "#",
        ]) ?></div>
        <div class="col-12 col-md-6 col-lg-3"><?= component("feature-card", [
            "icon" => "folder2",
            "title" => "Personal academic vault",
            "text" =>
                "Store private study bookmarks, upload approval logs, and monitor your NSBM honor verification standing.",
                            "linkLabel" => "View active pods",
            "linkHref" => "#",
        ]) ?></div>
    </div>
</section>