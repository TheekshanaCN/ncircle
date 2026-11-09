<?php
/**
 * components/avatar.php  -  initials in a circle
 *   <?= component('avatar', ['name' => 'Nimesh Perera', 'tone' => 'teal']) ?>
 * tone   teal | lime | pale | grey   (omit to pick one from the name, so a person keeps a colour)
 * size   sm | md (default) | lg
 * ring   true = lime outline, as in the top bar
 */
$name = $name ?? "";
$tones = ["teal", "lime", "grey", "pale"];
$tone = $tone ?? $tones[abs(crc32((string) $name)) % count($tones)];
$size = $size ?? "md";
$ring = !empty($ring);
?>
<span class="sc-person sc-person--<?= e($tone) ?> sc-person--<?= e($size) ?><?= $ring ? " sc-person--ring" : "" ?>" aria-hidden="true"><?= e(initials($name)) ?></span>
