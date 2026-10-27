<?php
/**
 * components/rating-stars.php
 *
 * Read-only (lists, cards):
 *   <?= component('rating-stars', ['rating' => 4.3, 'count' => 12]) ?>
 *
 * Clickable (resource page). Renders 5 buttons; the Ratings owner wires
 * assets/js/modules/rating.js to send  SC.request($url, ...)  with the clicked data-value:
 *   <?= component('rating-stars', ['rating' => $myRating, 'interactive' => true, 'url' => url('/api/resources/' . $id . '/rate')]) ?>
 *
 * rating 0-5 (halves allowed)   count = number of ratings (omit to hide the text)
 */
$rating      = max(0.0, min(5.0, (float) ($rating ?? 0)));
$count       = $count ?? null;
$interactive = !empty($interactive);
$url         = $url ?? '';
?>
<?php if ($interactive): ?>
<span class="sc-rating sc-rating--interactive" role="group" aria-label="Rate this resource"<?= html_attrs(['data-rating-url' => $url]) ?>>
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <button type="button" class="sc-rating__star<?= $rating >= $i ? ' is-active' : '' ?>" data-value="<?= $i ?>" aria-label="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>"><i class="bi bi-star-fill" aria-hidden="true"></i></button>
    <?php endfor; ?>
</span>
<?php else: ?>
<span class="sc-rating sc-rating--readonly" role="img" aria-label="<?= e(number_format($rating, 1)) ?> out of 5">
    <?php for ($i = 1; $i <= 5; $i++):
        $half = $rating < $i && $rating >= $i - 0.5; ?>
        <i class="bi bi-star<?= $half ? '-half' : '-fill' ?> sc-rating__star<?= $rating >= $i - 0.5 ? ' is-active' : '' ?>" aria-hidden="true"></i>
    <?php endfor; ?>
    <?php if ($count !== null): ?>
        <span class="sc-rating__meta"><?= (int) $count === 0 ? 'No ratings yet' : e(number_format($rating, 1)) . ' (' . (int) $count . ')' ?></span>
    <?php endif; ?>
</span>
<?php endif; ?>
