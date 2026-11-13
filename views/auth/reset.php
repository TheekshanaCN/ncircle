<?php
/** views/auth/reset.php  -  choose a new password from an emailed link */
ob_start();
?>
<h1 class="sc-auth__title">Choose a new password</h1>
<p class="sc-auth__sub">Use 8+ characters with an uppercase letter and a number.</p>

<?php include BASE_PATH . "/views/partials/flash.php"; ?>

<form method="post" action="<?= e(url("/reset-password")) ?>" class="sc-auth__form sc-auth__form--filled">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= e($token) ?>">
    <?= component("form-field", ["name" => "password", "label" => "New password", "type" => "password", "icon" => "lock", "toggle" => true, "attrs" => ["required" => true, "autocomplete" => "new-password"]]) ?>
    <?= component("form-field", ["name" => "password_confirmation", "label" => "Confirm password", "type" => "password", "icon" => "shield-check", "attrs" => ["required" => true, "autocomplete" => "new-password"]]) ?>
    <?= component("button", ["label" => "Update password", "type" => "submit", "variant" => "dark", "class" => "w-100"]) ?>
</form>
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
