<?php
/** views/auth/register.php  -  Create account page (Figma frame "sign-up") */
$rule = fn (string $name, string $label) =>
    '<span data-rule="' . e($name) . '"><i class="bi bi-circle" aria-hidden="true"></i> ' . e($label) . '</span>';
$meter = '<div class="sc-pw-meter" aria-hidden="true"><span class="sc-pw-meter__fill"></span></div>'
    . '<div class="sc-pw-rules">' . $rule("length", "8+ characters") . $rule("upper", "Uppercase letter") . $rule("number", "At least 1 number") . '</div>';
$yearOptions = config("app.years");
ob_start();
?>
<h1 class="sc-auth__title">Create account</h1>
<p class="sc-auth__sub">Sign up with your NSBM student account.</p>

<?php include BASE_PATH . "/views/partials/flash.php"; ?>

<?= component("button", [
    "label" => "Register with NSBM Student Account",
    "variant" => "soft",
    "class" => "w-100 sc-ms-btn",
    "attrs" => ["data-toast" => "Office 365 sign-up is not connected yet. Please use the form below."],
]) ?>
<div class="sc-auth__divider sc-auth__divider--caps"><span>Or register with student credentials</span></div>

<form method="post" action="<?= e(url("/register")) ?>" class="sc-auth__form sc-auth__form--compact">
    <?= csrf_field() ?>
    <?= component("form-field", ["name" => "name", "label" => "Full Name", "icon" => "person", "placeholder" => "Kavindu Senarathna", "attrs" => ["autocomplete" => "name", "required" => true, "maxlength" => 100]]) ?>
    <div class="row gx-3 gy-0">
        <div class="col-sm-6"><?= component("form-field", ["name" => "student_id", "label" => "NSBM Student ID", "icon" => "person-vcard", "placeholder" => "0000", "attrs" => ["required" => true, "maxlength" => 20]]) ?></div>
        <div class="col-sm-6"><?= component("form-field", ["name" => "email", "label" => "University Email", "type" => "email", "icon" => "envelope", "placeholder" => "kavindu@students.nsbm.ac.lk", "attrs" => ["autocomplete" => "email", "required" => true, "maxlength" => 120]]) ?></div>
        <div class="col-sm-6"><?= component("form-field", ["name" => "faculty", "label" => "Faculty", "type" => "select", "icon" => "bank", "placeholder" => "Select Faculty", "options" => config("app.faculties"), "attrs" => ["required" => true]]) ?></div>
        <div class="col-sm-6"><?= component("form-field", ["name" => "academic_year", "label" => "Academic Year / Intake", "type" => "select", "icon" => "calendar3", "placeholder" => "Select Academic Year", "options" => $yearOptions, "attrs" => ["required" => true]]) ?></div>
    </div>
    <?= component("form-field", ["name" => "password", "label" => "Create Password", "type" => "password", "icon" => "lock", "toggle" => true, "labelAside" => " ", "after" => $meter, "attrs" => ["autocomplete" => "new-password", "required" => true, "data-password-meter" => true]]) ?>
    <div class="sc-pw-confirm">
        <?= component("form-field", ["name" => "password_confirmation", "label" => "Confirm Password", "type" => "password", "icon" => "shield-check", "attrs" => ["autocomplete" => "new-password", "required" => true, "data-password-confirm" => true]]) ?>
        <i class="bi bi-check-lg sc-pw-confirm__tick" data-password-match hidden aria-hidden="true"></i>
    </div>

    <div class="form-check sc-auth__honor">
        <input class="form-check-input<?= field_error("honor") !== "" ? " is-invalid" : "" ?>" type="checkbox" value="1" name="honor" id="field-honor"<?= old("honor") ? " checked" : "" ?> required>
        <label class="form-check-label" for="field-honor">I accept the <a href="<?= e(url("/integrity")) ?>" target="_blank" rel="noopener">NSBM Academic Honor Code</a> and agree to peer copyright and study ethics guidelines.</label>
        <?php if (field_error("honor") !== ""): ?><div class="sc-field__error"><i class="bi bi-exclamation-circle" aria-hidden="true"></i> <?= e(field_error("honor")) ?></div><?php endif; ?>
    </div>

    <?= component("button", ["label" => "Create account", "type" => "submit", "variant" => "dark", "icon" => "person-check", "iconPosition" => "end", "class" => "w-100"]) ?>
</form>
<?= component("button", ["label" => "Already registered? Sign in", "variant" => "soft", "icon" => "box-arrow-in-right", "iconPosition" => "end", "href" => url("/login"), "class" => "w-100 mt-2 sc-auth__secondary"]) ?>
<?php
$form = ob_get_clean();
echo component("auth-shell", [
    "image" => "img/sign-up.webp",
    "imageSize" => [729, 729],
    "imageAlt" => "A student taking notes during an online lecture",
    "headline" => 'Your study <span class="sc-script">Ncircle.</span> <span class="d-block">Starts here.</span>',
    "lead" => "Get peer-verified summaries, lecture audio timestamps, and past paper answers uploaded weekly by fellow Green University students.",
    "form" => $form,
    "reverse" => true,
    "wide" => true,
]);
