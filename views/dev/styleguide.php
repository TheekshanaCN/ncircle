<?php
/**
 * views/dev/styleguide.php  -  every shared component on one page (UI lead only)
 * Open /dev/styleguide (only works while APP_DEBUG is true).
 * Before building any UI, look here first. Copy the call, change the values.
 */
$sampleResource = [
    "id" => 12,
    "title" => "Software Engineering: Week 6 lecture notes",
    "subject" => "SE301",
    "category" => "Lecture notes",
    "file_type" => "pdf",
    "uploader" => "Nimal P.",
    "rating" => 4.3,
    "rating_count" => 12,
    "downloads" => 238,
    "status" => "pending",
    "created_at" => "2026-10-20",
]; ?>
<?= component("page-header", [
    "title" => "Style guide",
    "subtitle" =>
        "Every shared component. Use these instead of writing your own markup.",
    "actions" =>
        component("button", [
            "label" => "Upload note",
            "icon" => "upload",
            "href" => "#",
        ]) .
        component("button", [
            "label" => "Delete demo",
            "variant" => "danger",
            "attrs" => [
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#demoModal",
            ],
        ]),
]) ?>

<section class="mb-5">
    <h2 class="h4 mb-3">Buttons</h2>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <?= component("button", ["label" => "Primary"]) ?>
        <?= component("button", [
            "label" => "Outline",
            "variant" => "outline",
        ]) ?>
        <?= component("button", ["label" => "Danger", "variant" => "danger"]) ?>
        <?= component("button", ["label" => "Link", "variant" => "link"]) ?>
        <?= component("button", [
            "label" => "With icon",
            "icon" => "arrow-right",
            "iconPosition" => "end",
        ]) ?>
        <?= component("button", ["label" => "Small", "size" => "sm"]) ?>
        <?= component("button", [
            "label" => "Disabled",
            "attrs" => ["disabled" => true],
        ]) ?>
        <span class="sc-demo-teal p-3 rounded-4"><?= component("button", [
            "label" => "On teal",
            "variant" => "light",
        ]) ?></span>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Status badges, tags and chips</h2>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <?= component("status-badge", ["status" => "pending"]) ?>
        <?= component("status-badge", ["status" => "approved"]) ?>
        <?= component("status-badge", ["status" => "rejected"]) ?>
        <?= component("status-badge", ["status" => "blocked"]) ?>
        <span class="sc-tag">+12.5%</span>
        <span class="sc-tag sc-tag--down">-3.2%</span>
        <span class="sc-tag sc-tag--outline">Outline</span>
        <span class="sc-chip">SE301</span>
        <span class="sc-chip sc-chip--mono">PDF</span>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Alerts</h2>
    <?= component("alert", [
        "type" => "success",
        "message" => "Uploaded. An admin will review it soon.",
    ]) ?>
    <?= component("alert", [
        "type" => "danger",
        "title" => "Could not save",
        "message" => "The file is larger than 10 MB.",
        "dismissible" => true,
    ]) ?>
    <?= component("alert", [
        "type" => "warning",
        "message" => "Your note is waiting for approval.",
    ]) ?>
    <?= component("alert", [
        "type" => "info",
        "message" => "You can rate a note once.",
    ]) ?>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Stat cards</h2>
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
            "icon" => "chat-dots",
            "value" => 843,
            "label" => "Questions answered",
            "tag" => "-3.2%",
            "down" => true,
        ]) ?></div>
        <div class="col-12 col-sm-6 col-lg-3"><?= component("stat-card", [
            "icon" => "diagram-3",
            "value" => "64",
            "label" => "Study groups",
            "tag" => "Active",
        ]) ?></div>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Rating stars</h2>
    <div class="d-flex flex-wrap gap-4 align-items-center">
        <?= component("rating-stars", ["rating" => 4.3, "count" => 12]) ?>
        <?= component("rating-stars", ["rating" => 2, "count" => 3]) ?>
        <?= component("rating-stars", ["rating" => 0, "count" => 0]) ?>
        <?= component("rating-stars", [
            "rating" => 3,
            "interactive" => true,
            "url" => "/api/resources/12/rate",
        ]) ?>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Resource cards</h2>
    <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-4"><?= component("resource-card", [
            "resource" => $sampleResource,
        ]) ?></div>
        <div class="col-12 col-md-6 col-lg-4"><?= component("resource-card", [
            "resource" => array_merge($sampleResource, [
                "title" => "Databases past paper 2025",
                "subject" => "DB202",
                "file_type" => "docx",
                "rating" => 5,
                "status" => "approved",
            ]),
            "showStatus" => true,
        ]) ?></div>
        <div class="col-12 col-md-6 col-lg-4"><?= component("resource-card", [
            "resource" => [
                "id" => 3,
                "title" =>
                    "A note with a very long title that has to wrap onto two lines to prove the card still looks tidy",
                "file_type" => "pptx",
            ],
        ]) ?></div>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Form fields</h2>
    <form class="row g-3" onsubmit="return false">
        <div class="col-md-6"><?= component("form-field", [
            "name" => "email",
            "label" => "University email",
            "type" => "email",
            "icon" => "envelope",
            "placeholder" => "s20481@students.nsbm.ac.lk",
            "required" => true,
        ]) ?></div>
        <div class="col-md-6"><?= component("form-field", [
            "name" => "password",
            "label" => "Password",
            "type" => "password",
            "icon" => "shield-check",
            "toggle" => true,
            "help" => "At least 8 characters.",
        ]) ?></div>
        <div class="col-md-6"><?= component("form-field", [
            "name" => "bad",
            "label" => "With an error",
            "type" => "password",
            "icon" => "lock",
            "toggle" => true,
            "error" => "That password is incorrect.",
        ]) ?></div>
        <div class="col-md-6"><?= component("form-field", [
            "name" => "subject_id",
            "label" => "Subject",
            "type" => "select",
            "placeholder" => "Choose a subject",
            "options" => [
                1 => "Software Engineering",
                2 => "Databases",
                3 => "Business Management",
            ],
            "value" => 2,
        ]) ?></div>
        <div class="col-12"><?= component("form-field", [
            "name" => "description",
            "label" => "Description",
            "type" => "textarea",
            "rows" => 3,
            "placeholder" => "What is in this note?",
        ]) ?></div>
        <div class="col-md-6"><?= component("form-field", [
            "name" => "file",
            "label" => "File",
            "type" => "file",
            "attrs" => ["accept" => ".pdf,.docx,.pptx"],
            "help" => "PDF, DOCX or PPTX, up to 10 MB.",
        ]) ?></div>
        <div class="col-md-6"><?= component("form-field", [
            "name" => "remember",
            "label" => "Remember me",
            "type" => "checkbox",
        ]) ?></div>
    </form>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Empty state</h2>
    <div class="sc-card">
        <?= component("empty-state", [
            "icon" => "folder2-open",
            "title" => "No notes yet",
            "text" => "Be the first to share a note for this subject.",
            "actionLabel" => "Upload a note",
            "actionHref" => "#",
        ]) ?>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Step cards</h2>
    <div class="row g-4">
        <div class="col-12 col-lg-4"><?= component("step-card", [
            "number" => 1,
            "icon" => "upload",
            "title" => "Upload your note",
            "text" =>
                "Drop PDF, DOCX, or PPTX slides with module codes, lecture weeks, and relevant professor notes.",
        ]) ?></div>
        <div class="col-12 col-lg-4"><?= component("step-card", [
            "number" => 2,
            "icon" => "patch-check",
            "title" => "Peer & batch rep review",
            "text" =>
                "Elected module representatives check formatting, academic ethics, citation accuracy, and legibility.",
        ]) ?></div>
        <div class="col-12 col-lg-4"><?= component("step-card", [
            "number" => 3,
            "icon" => "arrow-left-right",
            "title" => "Knowledge exchange",
            "text" =>
                "Peers download your material, ask contextual questions, and award star contributor badges.",
        ]) ?></div>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Feature cards</h2>
    <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-3"><?= component("feature-card", [
            "icon" => "journal-text",
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
            "icon" => "chat-square-text",
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
        ]) ?></div>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Pagination</h2>
    <?= component("pagination", [
        "page" => 5,
        "pages" => 12,
        "baseUrl" => url("/dev/styleguide"),
        "query" => [
            "q" => "java",
        ],
    ]) ?>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Section heading</h2>
    <?= component("section-head", [
        "eyebrow" => "Academic structure",
        "title" => "Tailored to the NSBM degree pathway",
        "subtitle" =>
            "Engineered around degree modules from Year 01 lectures through to final-year capstone defense.",
    ]) ?>
</section>

<?= component("confirm-modal", [
    "id" => "demoModal",
    "title" => "Delete this note?",
    "message" => "This cannot be undone.",
    "action" => "#",
    "confirmLabel" => "Delete",
]) ?>
