<?php
/**
 * components/attachment-list.php  -  images, GIFs and files on a post
 *   <?= component('attachment-list', ['attachments' => $post['attachments']]) ?>
 * Files always download. They never open inside the site.
 */
$attachments = $attachments ?? [];
$visual = array_filter($attachments, fn ($a) => $a["kind"] !== "file");
$files = array_filter($attachments, fn ($a) => $a["kind"] === "file");
$icons = ["pdf" => "file-earmark-pdf", "docx" => "file-earmark-word", "pptx" => "file-earmark-slides", "xlsx" => "file-earmark-excel", "txt" => "file-earmark-text"];
?>
<?php if ($visual): ?>
    <div class="sc-media">
        <?php foreach ($visual as $a): ?>
            <?php if ($a["kind"] === "gif"): ?>
                <figure class="sc-media__item">
                    <img src="<?= e(GiphyService::url($a["giphy_id"])) ?>" alt="GIF: <?= e($a["original_name"]) ?>" loading="lazy" referrerpolicy="no-referrer"<?= $a["width"] ? ' width="' . (int) $a["width"] . '" height="' . (int) $a["height"] . '"' : "" ?>>
                    <figcaption>via GIPHY</figcaption>
                </figure>
            <?php else: ?>
                <a class="sc-media__item" href="<?= e(url("/attachments/" . (int) $a["id"])) ?>" data-lightbox>
                    <img src="<?= e(url("/attachments/" . (int) $a["id"])) ?>" alt="Shared image: <?= e($a["original_name"]) ?>" loading="lazy"<?= $a["width"] ? ' width="' . (int) $a["width"] . '" height="' . (int) $a["height"] . '"' : "" ?>>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if ($files): ?>
    <ul class="sc-files">
        <?php foreach ($files as $a): $ext = strtolower(pathinfo($a["original_name"], PATHINFO_EXTENSION)); ?>
            <li>
                <a class="sc-files__item" href="<?= e(url("/attachments/" . (int) $a["id"])) ?>" download>
                    <i class="bi bi-<?= e($icons[$ext] ?? "file-earmark") ?>" aria-hidden="true"></i>
                    <span class="sc-files__name"><?= e($a["original_name"]) ?></span>
                    <small><?= e(strtoupper($ext)) ?> &middot; <?= e(format_bytes((int) $a["size"])) ?></small>
                    <i class="bi bi-download" aria-hidden="true"></i>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
