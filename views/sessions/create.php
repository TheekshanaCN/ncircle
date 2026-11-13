<?php
/** views/sessions/create.php  -  schedule or edit a meeting. $meeting is null when creating. */
$editing = $meeting !== null;
$backUrl = $group !== null ? url("/groups/" . (int) $group["id"]) : url("/sessions");
$value = fn (string $key, $fallback = "") => old($key, $editing ? ($meeting[$key] ?? $fallback) : $fallback);
$start = $editing ? strtotime($meeting["starts_at"]) : null;
$end = $editing && !empty($meeting["ends_at"]) ? strtotime($meeting["ends_at"]) : null;
?>
<?= component("page-header", [
    "title" => $editing ? "Edit meeting" : "Schedule a meeting",
    "subtitle" => $group !== null ? "For " . $group["name"] . ". All members get a notification." : "Open to students in your faculty. You take the first seat.",
    "actions" => component("button", ["label" => "Back", "icon" => "arrow-left", "variant" => "outline", "size" => "sm", "href" => $backUrl, "class" => "sc-btn-quiet"]),
]) ?>
<div class="sc-form-narrow">
    <form class="sc-panel" method="post" action="<?= e(url($editing ? "/sessions/" . (int) $meeting["id"] : "/sessions")) ?>">
        <?= csrf_field() ?>
        <?php if (!$editing && $group !== null): ?><input type="hidden" name="group_id" value="<?= (int) $group["id"] ?>"><?php endif; ?>
        <?= component("form-field", ["name" => "title", "label" => "Title", "value" => $value("title"), "placeholder" => "Database Normalization Problem Solving Sprint", "required" => true, "attrs" => ["maxlength" => 160]]) ?>
        <div class="row g-3">
            <?php if ($group === null): ?>
                <div class="col-md-6"><?= component("form-field", ["name" => "subject_id", "label" => "Subject", "type" => "select", "placeholder" => "Choose a subject", "options" => $subjects, "value" => $value("subject_id"), "required" => true]) ?></div>
            <?php endif; ?>
            <div class="col-md-6"><?= component("form-field", ["name" => "mode", "label" => "Meeting type", "type" => "select", "placeholder" => "Online or physical?", "options" => config("app.session_modes"), "value" => $value("mode"), "required" => true, "attrs" => ["data-meeting-mode" => true]]) ?></div>
            <div class="col-md-4"><?= component("form-field", ["name" => "date", "label" => "Date", "type" => "date", "value" => old("date", $start ? date("Y-m-d", $start) : ""), "required" => true, "attrs" => ["min" => date("Y-m-d")]]) ?></div>
            <div class="col-md-4"><?= component("form-field", ["name" => "start_time", "label" => "Start time", "type" => "time", "value" => old("start_time", $start ? date("H:i", $start) : ""), "required" => true]) ?></div>
            <div class="col-md-4"><?= component("form-field", ["name" => "end_time", "label" => "End time", "type" => "time", "value" => old("end_time", $end ? date("H:i", $end) : ""), "required" => true]) ?></div>
        </div>
        <div data-mode-field="physical hybrid"><?= component("form-field", ["name" => "location", "label" => "Place", "icon" => "geo-alt", "value" => $value("location"), "placeholder" => "Faculty of Computing, Lab 04", "attrs" => ["maxlength" => 160]]) ?></div>
        <div data-mode-field="online hybrid"><?= component("form-field", ["name" => "meeting_url", "label" => "Meeting link", "icon" => "camera-video", "value" => $value("meeting_url"), "placeholder" => "https://meet.google.com/abc-defg-hij", "help" => "Only people going can see this.", "attrs" => ["maxlength" => 500, "inputmode" => "url"]]) ?></div>
        <div class="row g-3">
            <div class="col-md-4"><?= component("form-field", ["name" => "capacity", "label" => "Seats", "type" => "number", "value" => $value("capacity", "10"), "required" => true, "attrs" => ["min" => 2, "max" => 200]]) ?></div>
        </div>
        <?= component("form-field", ["name" => "description", "label" => "Details", "type" => "textarea", "rows" => 3, "value" => $value("description"), "placeholder" => "What will you cover, and what should people bring?", "required" => true, "attrs" => ["maxlength" => 500]]) ?>
        <?= component("button", ["label" => $editing ? "Save changes" : "Schedule meeting", "type" => "submit", "variant" => "dark"]) ?>
    </form>
</div>
