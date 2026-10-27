<?php
/**
 * components/pagination.php  -  numbered pages, keeps your filters in the links
 *
 *   <?= component('pagination', [
 *         'page'    => 3,                        // current page (1-based)
 *         'pages'   => 12,                       // total pages
 *         'baseUrl' => url('/resources'),
 *         'query'   => ['subject' => 4, 'q' => 'java'],   // other GET params to keep
 *   ]) ?>
 *
 * Prints nothing when there is only one page. Links use ?page=N.
 */
$page    = max(1, (int) ($page ?? 1));
$pages   = max(1, (int) ($pages ?? 1));
$baseUrl = $baseUrl ?? '';
$query   = $query ?? [];

if ($pages <= 1) {
    return;
}
$page = min($page, $pages);

$href = fn (int $p): string => $baseUrl . '?' . http_build_query(array_merge($query, ['page' => $p]));

// 1 ... (page-1) page (page+1) ... last
$items = [];
for ($p = 1; $p <= $pages; $p++) {
    if ($p === 1 || $p === $pages || abs($p - $page) <= 1) {
        $items[] = $p;
    } elseif (end($items) !== '…') {
        $items[] = '…';
    }
}
?>
<nav class="sc-pagination" aria-label="Pagination">
    <ul class="pagination justify-content-center mb-0">
        <li class="page-item<?= $page === 1 ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= $page === 1 ? '#' : e($href($page - 1)) ?>" aria-label="Previous page"><i class="bi bi-chevron-left" aria-hidden="true"></i></a>
        </li>
        <?php foreach ($items as $item): ?>
            <?php if ($item === '…'): ?>
                <li class="page-item disabled"><span class="page-link">…</span></li>
            <?php else: ?>
                <li class="page-item<?= $item === $page ? ' active' : '' ?>"<?= $item === $page ? ' aria-current="page"' : '' ?>>
                    <a class="page-link" href="<?= e($href($item)) ?>"><?= $item ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
        <li class="page-item<?= $page === $pages ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= $page === $pages ? '#' : e($href($page + 1)) ?>" aria-label="Next page"><i class="bi bi-chevron-right" aria-hidden="true"></i></a>
        </li>
    </ul>
</nav>
