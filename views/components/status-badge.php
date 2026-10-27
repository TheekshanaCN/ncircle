<?php
/**
 * components/status-badge.php  -  pending / approved / rejected / blocked
 *
 *   <?= component('status-badge', ['status' => $resource['status']]) ?>
 *
 * 'active' is shown like 'approved'. Anything unknown is shown like 'blocked'.
 */
$status = strtolower((string) ($status ?? ''));
$map = [
    'pending'  => ['pending',  'hourglass-split', 'Pending'],
    'approved' => ['approved', 'check-circle',    'Approved'],
    'active'   => ['approved', 'check-circle',    'Active'],
    'rejected' => ['rejected', 'x-circle',        'Rejected'],
    'blocked'  => ['blocked',  'slash-circle',    'Blocked'],
];
[$modifier, $icon, $text] = $map[$status] ?? ['blocked', 'dash-circle', ucfirst($status)];
$label = $label ?? $text;
?>
<span class="sc-badge sc-badge--<?= e($modifier) ?>"><i class="bi bi-<?= e($icon) ?>" aria-hidden="true"></i> <?= e($label) ?></span>
