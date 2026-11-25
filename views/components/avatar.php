<?php
/**
 * components/avatar.php  -  profile photo, or initials, in a circle
 *   <?= component('avatar', ['name' => 'Nimesh Perera', 'tone' => 'teal']) ?>
 * photo  avatar_url(...) of the person, or null to show initials
 * tone   teal | lime | pale | grey   (omit to pick one from the name, so a person keeps a colour)
 * size   sm | md (default) | lg
 * ring   true = lime outline, as in the top bar
 */
$name = $name ?? "";
$photo = $photo ?? null;
$tones = ["teal", "lime", "grey", "pale"];
$tone = $tone ?? $tones[abs(crc32((string) $name)) % count($tones)];
$size = $size ?? "md";
$ring = !empty($ring);
?>
<span class="sc-person sc-person--<?= e($tone) ?> sc-person--<?= e($size) ?><?= $ring ? " sc-person--ring" : "" ?>" aria-hidden="true"><?php if ($photo): ?><img src="<?= e($photo) ?>" alt="" loading="lazy"><?php else: ?><?= e(initials($name)) ?><?php endif; ?></span>
