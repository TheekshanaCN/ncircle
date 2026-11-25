<?php
/**
 * components/auth-shell.php  -  the Figma sign-in / sign-up split screen
 *   <?= component('auth-shell', [
 *         'image' => 'img/sign-in.webp', 'imageAlt' => '…', 'imageSize' => [905, 836],
 *         'headline' => $trustedHtml, 'lead' => '…', 'form' => $trustedHtml, 'reverse' => false, 'wide' => false]) ?>
 * reverse = true puts the card on the left (sign up).    wide = the bigger card for long forms.
 */
$reverse = !empty($reverse);
$wide = !empty($wide);
$imageSize = $imageSize ?? [900, 800];
?>
<?= component("theme-toggle", ["class" => "sc-auth__theme"]) ?>
<div class="sc-auth<?= $reverse ? " sc-auth--reverse" : "" ?>">
    <aside class="sc-auth__showcase">
        <img class="sc-auth__art" src="<?= e(asset($image)) ?>" width="<?= (int) $imageSize[0] ?>" height="<?= (int) $imageSize[1] ?>" alt="<?= e($imageAlt ?? "") ?>">
        <div class="sc-auth__headline"><?= $headline ?></div>
        <p class="sc-auth__lead"><?= e($lead ?? "") ?></p>
    </aside>
    <section class="sc-auth__panel">
        <div class="sc-auth__card<?= $wide ? " sc-auth__card--wide" : "" ?>">
            <a class="sc-auth__logo" href="<?= e(url("/")) ?>"><img src="<?= e(asset("img/logo.svg")) ?>" alt="Ncircle home"></a>
            <?= $form ?>
        </div>
    </section>
</div>
