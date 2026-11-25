<?php
/**
 * components/note-row.php  -  compact note line used in Top-Rated Notes
 *   <?= component('note-row', ['resource' => $r]) ?>
 */
$resource = $resource ?? [];
$type = strtoupper(substr((string) ($resource["file_type"] ?? ""), 0, 3));
$pages = (int) ($resource["pages"] ?? 0);
?>
<article class="sc-note-row">
    <span class="sc-note-row__type"><?= e($type) ?></span>
    <div class="sc-note-row__body">
        <h3 class="sc-note-row__title"><a href="<?= e(url("/resources/" . (int) $resource["id"])) ?>"><?= e($resource["title"]) ?></a></h3>
        <p class="sc-note-row__by">by <?= e($resource["uploader"]) ?><?= $pages ? " &middot; " . $pages . " pgs" : "" ?></p>
    </div>
    <span class="sc-note-row__rating"><i class="bi bi-star" aria-hidden="true"></i><span class="visually-hidden">Rated</span> <?= e(number_format((float) $resource["rating"], 1)) ?></span>
    <a class="sc-note-row__download" href="<?= e(url("/resources/" . (int) $resource["id"] . "/download")) ?>" aria-label="Download <?= e($resource["title"]) ?>"><i class="bi bi-download" aria-hidden="true"></i></a>
</article>
