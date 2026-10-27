<?php
/**
 * Media box shows ONE of these (first one set wins):
 *   media   trusted HTML (anything you want inside the box)
 *   video   path to .mp4/.webm -> autoplay, muted, loop   (+ optional 'poster')
 *   image   path/URL           -> fills the box           (+ 'imageAlt')
 *   icon    Bootstrap Icons name -> lime icon on a teal tile (Figma illustration look)
 *   badges  [['Y1','dark'], ['Y2','dark'], ['Y3','lime'], ['Y4','outline']]  chips above the icon
 *
 *   <?= component('feature-card', [
 *         'icon'      => 'journal-text',
 *         'badges'    => [['Y1', 'dark'], ['Y2', 'dark'], ['Y3', 'lime'], ['Y4', 'outline']],
 *         'title'     => 'Note library (Year 1–4)',
 *         'text'      => 'Lectures, lab sheets, and past papers organized by official NSBM module codes and semester tracks.',
 *         'linkLabel' => 'Browse module repository',
 *         'linkHref'  => url('/resources'),
 *   ]) ?>
 *   <?= component('feature-card', ['image' => asset('img/forum.png'), 'imageAlt' => 'Forum', 'title' => '…', 'text' => '…']) ?>
 *   <?= component('feature-card', ['video' => asset('video/pods.mp4'), 'poster' => asset('img/pods.jpg'), 'title' => '…', 'text' => '…']) ?>
 *
 * linkLabel + linkHref are optional (no link row if either is empty).
 * Grid: <div class="col-12 col-md-6 col-lg-3"> ... </div>
 */
$media = $media ?? "";
$video = $video ?? "";
$poster = $poster ?? "";
$image = $image ?? "";
$imageAlt = $imageAlt ?? "";
$icon = $icon ?? "";
$badges = $badges ?? [];
$title = $title ?? "";
$text = $text ?? "";
$linkLabel = $linkLabel ?? "";
$linkHref = $linkHref ?? "";
$hasLink = $linkLabel !== "" && $linkHref !== "";
$hasMedia =
    $media !== "" || $video !== "" || $image !== "" || $icon !== "" || $badges;
?>
<article class="sc-card sc-card--lift sc-feature">
    <?php if ($hasMedia): ?>
    <div class="sc-feature__media">
        <?php if ($media !== ""): ?>
            <?= $media ?>
        <?php elseif ($video !== ""): ?>
            <video class="sc-feature__cover" src="<?= e(
                $video,
            ) ?>"<?= $poster !== ""
    ? ' poster="' . e($poster) . '"'
    : "" ?> autoplay muted loop playsinline aria-hidden="true"></video>
        <?php elseif ($image !== ""): ?>
            <img class="sc-feature__cover" src="<?= e($image) ?>" alt="<?= e(
    $imageAlt,
) ?>" loading="lazy">
        <?php else: ?>
            <?php if ($badges): ?>
                <div class="sc-feature__badges">
                    <?php foreach ($badges as $badge): ?>
                        <span class="sc-feature__badge sc-feature__badge--<?= e(
                            $badge[1] ?? "dark",
                        ) ?>"><?= e((string) $badge[0]) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($icon !== ""): ?>
                <span class="sc-feature__tile"><i class="bi bi-<?= e(
                    $icon,
                ) ?>" aria-hidden="true"></i></span>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <h3 class="sc-feature__title"><?= e($title) ?></h3>
    <p class="sc-feature__text"><?= e($text) ?></p>

    <?php if ($hasLink): ?>
        <div class="sc-feature__foot">
            <a class="sc-feature__link stretched-link" href="<?= e(
                $linkHref,
            ) ?>"><?= e(
    $linkLabel,
) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    <?php endif; ?>
</article>
