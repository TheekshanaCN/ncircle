<?php
/** views/auth/check-inbox.php  -  "Check your inbox" state from Figma */
ob_start();
?>
<h1 class="sc-auth__title">Check your inbox</h1>
<p class="sc-auth__sub">One more step to activate your account.</p>

<?php include BASE_PATH . "/views/partials/flash.php"; ?>

<div class="sc-inbox">
    <span class="sc-inbox__icon"><i class="bi bi-envelope-check" aria-hidden="true"></i></span>
    <?php if ($sent): ?>
        <h2 class="sc-inbox__title">Verification link sent!</h2>
        <p class="sc-inbox__text">We sent an activation link to <strong><?= e($email) ?></strong>.</p>
    <?php else: ?>
        <h2 class="sc-inbox__title">Link not sent yet</h2>
        <p class="sc-inbox__text">Your account is saved. Use Resend to get an activation link at <strong><?= e($email) ?></strong>.</p>
    <?php endif; ?>

    <form method="post" action="<?= e(url("/verify-email/resend")) ?>" class="sc-inbox__resend" data-resend-wait="<?= (int) $wait ?>">
        <?= csrf_field() ?>
        <button type="submit"><i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Resend<span data-resend-label> available in <code data-resend-time>00:<?= sprintf("%02d", (int) $wait) ?>s</code></span></button>
    </form>

    <?= component("button", ["label" => "Open Outlook / Webmail", "variant" => "dark", "href" => "https://outlook.office.com/mail/", "class" => "w-100", "attrs" => ["target" => "_blank", "rel" => "noopener"]]) ?>
</div>

<?php if ($devLink): ?>
    <div class="sc-dev-note">
        <strong>Local development</strong>
        <p>No mail server is set up, so emails go to <code>storage/logs/mail.log</code>. Use this link to verify:</p>
        <a href="<?= e($devLink) ?>">Verify this account</a>
    </div>
<?php endif; ?>

<p class="sc-auth__legal"><a class="sc-auth__plain" href="<?= e(url("/login")) ?>">Back to sign in</a></p>
<?php
$form = ob_get_clean();
echo component("auth-shell", [
    "image" => "img/sign-up.webp",
    "imageSize" => [729, 729],
    "imageAlt" => "A student taking notes during an online lecture",
    "headline" => 'Almost in the <span class="sc-script">Ncircle.</span>',
    "lead" => "Verifying your NSBM email keeps this community to real students.",
    "form" => $form,
]);
