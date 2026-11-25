<?php /** views/home/index.php  -  landing page, one file per section */ ?>
<div data-live-root data-live-url="<?= e(url("/api/stats/landing")) ?>">
<?php include __DIR__ . "/sections/hero.php"; ?>
<?php include __DIR__ . "/sections/metrics.php"; ?>
<?php include __DIR__ . "/sections/features.php"; ?>
<?php include __DIR__ . "/sections/dashboard-preview.php"; ?>
<?php include __DIR__ . "/sections/steps.php"; ?>
<?php include __DIR__ . "/sections/cta.php"; ?>
</div>
