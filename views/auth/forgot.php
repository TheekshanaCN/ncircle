<?php
/** views/auth/forgot.php  -  ask for a password reset link */
ob_start();
?>
<h1 class="sc-auth__title">Forgot password?</h1>
<p class="sc-auth__sub">Enter your university email and we will send a reset link.</p>

<?php include BASE_PATH . "/views/partials/flash.php"; ?>

<form method="post" action="<?= e(url("/forgot-password")) ?>" class="sc-auth__form sc-auth__form--filled">
    <?= csrf_field() ?>
    <?= component("form-field", ["name" => "email", "label" => "University email", "type" => "email", "icon" => "at", "placeholder" => "s20481@students.nsbm.ac.lk", "attrs" => ["required" => true, "autocomplete" => "email"]]) ?>
    <?= component("button", ["label" => "Send reset link", "type" => "submit", "variant" => "dark", "icon" => "arrow-right", "iconPosition" => "end", "class" => "w-100"]) ?>
</form>

<?php if ($devLink): ?>
    <div class="sc-dev-note">
        <strong>Local development</strong>
        <p>No mail server is set up, so emails go to <code>storage/logs/mail.log</code>.</p>
        <a href="<?= e($devLink) ?>">Open the reset link</a>
    </div>
<?php endif; ?>

<p class="sc-auth__legal"><a class="sc-auth__plain" href="<?= e(url("/login")) ?>">Back to sign in</a></p>
<?php
$form = ob_get_clean();
echo component("auth-shell", [
    "image" => "img/sign-in.png",
    "imageSize" => [905, 836],
    "imageAlt" => "Two students studying at a library table",
    "headline" => 'Back to the <span class="sc-script">Ncircle.</span>',
    "lead" => "It happens to everyone. Choose a new password and carry on studying.",
    "form" => $form,
]);
