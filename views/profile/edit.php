<?php
/** views/profile/edit.php  -  settings: profile details and password */
$posted = old("name") !== "";   // after a failed save, ticked boxes come from the form, not the database
?>
<?= component("page-header", ["title" => "Settings", "subtitle" => "Your profile details and password."]) ?>

<div class="sc-form-layout sc-form-layout--even">
    <form class="sc-panel" method="post" action="<?= e(url("/profile/edit")) ?>">
        <?= csrf_field() ?>
        <h2 class="sc-panel__title mb-3">Profile</h2>
        <?= component("form-field", ["name" => "name", "label" => "Full name", "icon" => "person", "value" => old("name", $user["name"]), "required" => true, "attrs" => ["maxlength" => 100]]) ?>
        <?= component("form-field", ["name" => "email_display", "label" => "University email", "icon" => "envelope", "value" => $user["email"], "attrs" => ["disabled" => true], "help" => "Your email and student ID cannot be changed."]) ?>
        <div class="row g-3">
            <div class="col-sm-6"><?= component("form-field", ["name" => "faculty_display", "label" => "Faculty", "icon" => "bank", "value" => config("app.faculties")[$user["faculty"]] ?? $user["faculty"], "attrs" => ["disabled" => true], "help" => "Set by your account. Ask an admin if it is wrong."]) ?></div>
            <div class="col-sm-6"><?= component("form-field", ["name" => "academic_year", "label" => "Academic year", "type" => "select", "icon" => "calendar3", "options" => config("app.years"), "value" => old("academic_year", $user["academic_year"]), "required" => true]) ?></div>
        </div>
        <?= component("form-field", ["name" => "bio", "label" => "Bio", "type" => "textarea", "rows" => 3, "value" => old("bio", $user["bio"] ?? ""), "attrs" => ["maxlength" => 500]]) ?>

        <h2 class="sc-panel__title mb-3 mt-4">Find Members</h2>
        <?= component("form-field", ["name" => "skills", "label" => "Your skills", "icon" => "stars", "value" => old("skills", $user["skills"] ?? ""), "placeholder" => "Python, Market research, Statistics", "help" => "Separate skills with commas.", "attrs" => ["maxlength" => 255]]) ?>
        <?= component("form-field", ["name" => "open_to_help", "label" => "I'm open to helping with research and projects", "type" => "checkbox", "value" => $posted ? old("open_to_help") : $user["open_to_help"]]) ?>
        <?= component("form-field", ["name" => "listed_in_directory", "label" => "Show me in the member directory", "type" => "checkbox", "value" => $posted ? old("listed_in_directory") : $user["listed_in_directory"]]) ?>
        <?= component("button", ["label" => "Save profile", "type" => "submit", "variant" => "dark"]) ?>
    </form>

    <form class="sc-panel" method="post" action="<?= e(url("/settings/password")) ?>">
        <?= csrf_field() ?>
        <h2 class="sc-panel__title mb-3">Password</h2>
        <?= component("form-field", ["name" => "current_password", "label" => "Current password", "type" => "password", "icon" => "lock", "toggle" => true, "required" => true, "attrs" => ["autocomplete" => "current-password"]]) ?>
        <?= component("form-field", ["name" => "password", "label" => "New password", "type" => "password", "icon" => "shield-lock", "toggle" => true, "required" => true, "help" => "8+ characters with an uppercase letter and a number.", "attrs" => ["autocomplete" => "new-password"]]) ?>
        <?= component("form-field", ["name" => "password_confirmation", "label" => "Confirm new password", "type" => "password", "icon" => "shield-check", "required" => true, "attrs" => ["autocomplete" => "new-password"]]) ?>
        <?= component("button", ["label" => "Change password", "type" => "submit", "variant" => "dark"]) ?>
    </form>
</div>
