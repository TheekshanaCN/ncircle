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

<section class="mb-5">
    <h2 class="h4 mb-3">Extra button variants</h2>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <?= component("button", ["label" => "Dark", "variant" => "dark"]) ?>
        <?= component("button", ["label" => "Soft", "variant" => "soft"]) ?>
        <?= component("button", ["label" => "Quiet outline", "variant" => "outline", "class" => "sc-btn-quiet"]) ?>
        <?= component("button", ["label" => "Extra small", "variant" => "dark", "class" => "btn-xs"]) ?>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Avatars and state pills</h2>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <?= component("avatar", ["name" => "Nimesh Perera", "tone" => "teal", "ring" => true]) ?>
        <?= component("avatar", ["name" => "Tharushi Wijesinghe", "tone" => "lime"]) ?>
        <?= component("avatar", ["name" => "Kavindu Silva", "tone" => "pale", "size" => "sm"]) ?>
        <?= component("avatar", ["name" => "Sachini W", "tone" => "grey", "size" => "lg"]) ?>
        <span class="sc-state sc-state--lime">SOLVED IN 11 MINS</span>
        <span class="sc-state sc-state--dark">ACTIVE DISCUSSION</span>
        <span class="sc-state sc-state--muted">NEEDS ANSWER</span>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Stat tiles (dashboard)</h2>
    <div class="row g-3">
        <div class="col-md-4"><?= component("stat-tile", ["label" => "Notes shared", "value" => 2481, "tag" => "+12.5%", "footIcon" => "folder2", "foot" => "Active peer vaults across 6 faculties"]) ?></div>
        <div class="col-md-4"><?= component("stat-tile", ["label" => "Active students", "value" => 1236, "tag" => "+7.2%", "footIcon" => "dot", "foot" => "Live pod study active now"]) ?></div>
        <div class="col-md-4"><?= component("stat-tile", ["label" => "Questions answered", "value" => 843, "tag" => "-2.5%", "down" => true, "footIcon" => "patch-check", "foot" => "Median resolution: 14m"]) ?></div>
    </div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Stacked bars and progress</h2>
    <div class="sc-panel">
        <?= component("stacked-bars", ["height" => 140, "median" => 45, "items" => [
            ["label" => "Mon", "top" => 25, "bottom" => 40, "title" => "Mon: 5 notes, 8 answers"],
            ["label" => "Tue", "top" => 30, "bottom" => 55, "active" => true, "tooltip" => "Peak sprint: 6 notes / 11 answers", "title" => "Tue: 6 notes, 11 answers"],
            ["label" => "Wed", "top" => 40, "bottom" => 0, "title" => "Wed: 8 notes, 0 answers"],
        ]]) ?>
        <div class="sc-progress mt-3" role="progressbar" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100" aria-label="Demo progress"><span style="width: 68%"></span></div>
    </div>
</section>

<?php
$demoNote = [
    "id" => 12, "user_id" => 1, "subject_id" => 1, "title" => "Normalization Cheat Sheet — Database Systems",
    "description" => "Step-by-step canonical decomposition guide covering 1NF to BCNF with NSBM exam scenarios.",
    "uploader" => "Tharushi Wijesinghe", "uploader_username" => "tharushi_w", "created_at" => date("Y-m-d H:i:s", time() - 7200),
    "category" => "Study Guide", "category_slug" => "study-guide", "subject" => "CS204", "subject_name" => "Database Systems",
    "file_type" => "pdf", "file_size" => 2516582, "pages" => 24, "rating" => 4.9, "rating_count" => 126,
    "downloads_count" => 1820, "comments_count" => 18, "is_bookmarked" => 0,
];
?>
<link rel="stylesheet" href="<?= e(asset("css/pages/resources.css")) ?>">
<section class="mb-5">
    <h2 class="h4 mb-3">Find Members cards and live badge</h2>
    <div class="sc-card-grid">
        <?= component("member-card", ["member" => ["id" => 1, "name" => "Ishara Dias", "faculty" => "Business", "academic_year" => 1, "bio" => "Organizational behaviour and survey research.", "skills" => "Market research, Interviews", "open_to_help" => 1]]) ?>
        <?= component("request-card", ["request" => ["id" => 1, "type" => "research", "status" => "open", "created_at" => date("Y-m-d H:i:s", time() - 7200), "title" => "Need a Business student for market research", "description" => "A student budgeting app needs a market survey.", "faculties" => ["Business"], "skills" => "Surveys", "author" => "Kavindu Silva", "author_faculty" => "Computing", "responses_count" => 2]]) ?>
    </div>
    <p class="mt-3"><?= component("live-badge") ?></p>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Note card and note row</h2>
    <?= component("note-card", ["resource" => $demoNote]) ?>
    <div class="mt-3"><?= component("note-row", ["resource" => $demoNote]) ?></div>
</section>

<section class="mb-5">
    <h2 class="h4 mb-3">Thread, pod and group cards</h2>
    <div class="sc-card-grid">
        <?= component("thread-card", ["question" => ["id" => 1, "title" => "What is the concrete difference between 2NF and 3NF?", "subject" => "CS204", "subject_name" => "Database", "author" => "Sachini W", "lead_answerer" => "Lasith W", "created_at" => date("Y-m-d H:i:s", time() - 2700), "solved_at" => date("Y-m-d H:i:s", time() - 2040), "replies" => 5, "upvotes" => 32, "views" => 142]]) ?>
        <?= component("pod-card", ["session" => ["id" => 1, "host_id" => 0, "mode" => "hybrid", "starts_at" => date("Y-m-d 15:30:00", time() + 86400), "ends_at" => date("Y-m-d 17:00:00", time() + 86400), "location" => "Faculty of Computing, Lab 04", "meeting_url" => "https://meet.google.com/abc-defg-hij", "subject" => "CS204", "title" => "Normalization Problem Solving Sprint", "description" => "Bring last year's question 3.", "attendees" => 8, "capacity" => 10, "preview" => ["Tharushi W", "Kavindu S"], "my_status" => null]]) ?>
        <?= component("group-card", ["group" => ["id" => 1, "name" => "CS204 Exam Sprint Crew", "subject" => "CS204", "subject_name" => "Database Systems", "description" => "Weekly normalization and SQL drills.", "members_count" => 6, "max_members" => 12, "preview" => ["Tharushi W", "Nimesh P", "Kavindu S"], "is_member" => true]]) ?>
    </div>
</section>

<?= component("confirm-modal", [
    "id" => "demoModal",
    "title" => "Delete this note?",
    "message" => "This cannot be undone.",
    "action" => "#",
    "confirmLabel" => "Delete",
]) ?>
