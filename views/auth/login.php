<?php
/** views/auth/login.php  -  Sign in page (Figma frame "sign-in") */
$loginError = Session::peek("login_error");
ob_start();
?>
<h1 class="sc-auth__title">Sign in</h1>
<p class="sc-auth__sub">Sign in with your NSBM student account.</p>

<?php include BASE_PATH . "/views/partials/flash.php"; ?>
<?php if ($loginError): ?>
    <?= component("alert", ["type" => "danger", "message" => $loginError]) ?>
<?php endif; ?>

<form method="post" action="<?= e(url("/login")) ?>" class="sc-auth__form sc-auth__form--filled">
    <?= csrf_field() ?>
    <?= component("form-field", ["name" => "login", "label" => "University email or Student ID", "icon" => "at", "placeholder" => "s20481@students.nsbm.ac.lk", "attrs" => ["required" => true, "autocomplete" => "username", "autofocus" => true]]) ?>
    <?= component("form-field", ["name" => "password", "label" => "Password", "type" => "password", "icon" => "lock", "toggle" => true, "placeholder" => "Your password", "attrs" => ["required" => true, "autocomplete" => "current-password"]]) ?>

    <div class="sc-auth__row">
        <?= component("form-field", ["name" => "remember", "label" => "Remember me", "type" => "checkbox"]) ?>
        <a class="sc-auth__link" href="<?= e(url("/forgot-password")) ?>">Forgot password?</a>
    </div>

    <?= component("button", ["label" => "Sign in", "type" => "submit", "variant" => "dark", "icon" => "arrow-right", "iconPosition" => "end", "class" => "w-100"]) ?>
</form>

<div class="sc-auth__divider"><span>New to Ncircle?</span></div>
<?= component("button", ["label" => "Join the Ncircle", "variant" => "soft", "icon" => "person-plus", "iconPosition" => "end", "href" => url("/register"), "class" => "w-100"]) ?>

<p class="sc-auth__legal">
    By continuing you agree to the <a href="<?= e(url("/terms")) ?>">Terms</a> and <a href="<?= e(url("/privacy")) ?>">Privacy Policy</a>.<br>
    Need technical help? Visit the <a class="sc-auth__plain" href="<?= e(url("/help")) ?>">Help Center</a>.
</p>
<?php
$form = ob_get_clean();
echo component("auth-shell", [
    "image" => "img/sign-in.png",
    "imageSize" => [905, 836],
    "imageAlt" => "Two students studying at a library table",
    "headline" => 'Welcome back to the <span class="sc-script">Ncircle.</span>',
    "lead" => "Pick up where you left off — your notes, groups and discussions are waiting.",
    "form" => $form,
]);
