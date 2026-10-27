<?php
/**
 * components/resource-card.php  -  one study note in a list/grid
 *
 *   <div class="row g-4">
 *     <?php foreach ($resources as $r): ?>
 *       <div class="col-12 col-md-6 col-lg-4"><?= component('resource-card', ['resource' => $r]) ?></div>
 *     <?php endforeach; ?>
 *   </div>
 *
 * $resource keys the card reads (all optional except id + title):
 *   id, title, subject, category, file_type (pdf|docx|pptx|xlsx|zip...), uploader,
 *   rating (0-5), rating_count, downloads, status, created_at
 *
 * showStatus = true  also prints the status badge (use on "My uploads" and the admin approvals list)
 */
$resource   = $resource ?? [];
$showStatus = !empty($showStatus);

$id        = $resource['id'] ?? 0;
$fileType  = strtolower((string) ($resource['file_type'] ?? ''));
$icons     = [
    'pdf'  => 'file-earmark-pdf',
    'doc'  => 'file-earmark-word',  'docx' => 'file-earmark-word',
    'ppt'  => 'file-earmark-slides', 'pptx' => 'file-earmark-slides',
    'xls'  => 'file-earmark-excel', 'xlsx' => 'file-earmark-excel',
    'zip'  => 'file-earmark-zip',
];
$fileIcon  = $icons[$fileType] ?? 'file-earmark-text';
$created   = !empty($resource['created_at']) ? date('M j, Y', strtotime((string) $resource['created_at'])) : '';
?>
<article class="sc-card sc-card--lift sc-resource">
    <div class="sc-resource__top">
        <span class="sc-icon-circle sc-icon-circle--teal"><i class="bi bi-<?= e($fileIcon) ?>" aria-hidden="true"></i></span>
        <?php if ($showStatus && !empty($resource['status'])): ?>
            <?= component('status-badge', ['status' => $resource['status']]) ?>
        <?php elseif ($fileType !== ''): ?>
            <span class="sc-chip sc-chip--mono"><?= e(strtoupper($fileType)) ?></span>
        <?php endif; ?>
    </div>

    <h3 class="sc-resource__title">
        <a class="stretched-link" href="<?= e(url('/resources/' . $id)) ?>"><?= e($resource['title'] ?? 'Untitled') ?></a>
    </h3>

    <div class="sc-resource__meta">
        <?php if (!empty($resource['subject'])): ?><span class="sc-chip"><?= e($resource['subject']) ?></span><?php endif; ?>
        <?php if (!empty($resource['category'])): ?><span class="sc-tag sc-tag--outline"><?= e($resource['category']) ?></span><?php endif; ?>
    </div>

    <div class="sc-resource__foot">
        <?= component('rating-stars', ['rating' => $resource['rating'] ?? 0, 'count' => $resource['rating_count'] ?? 0]) ?>
        <span class="sc-resource__stat" title="Downloads"><i class="bi bi-download" aria-hidden="true"></i> <?= number_format((int) ($resource['downloads'] ?? 0)) ?></span>
    </div>

    <?php if (!empty($resource['uploader']) || $created !== ''): ?>
        <p class="sc-resource__by">
            <?php if (!empty($resource['uploader'])): ?>by <?= e($resource['uploader']) ?><?php endif; ?>
            <?php if ($created !== ''): ?> &middot; <?= e($created) ?><?php endif; ?>
        </p>
    <?php endif; ?>
</article>
