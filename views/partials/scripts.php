<?php /** views/partials/scripts.php  -  shared scripts, end of <body> */ ?>
<script src="<?= e(asset("vendor/bootstrap/bootstrap.bundle.min.js")) ?>"></script>
<script src="<?= e(asset("js/app.js")) ?>"></script>
<?php if (!empty($GLOBALS["sc_needs_composer"])): ?>
    <script src="<?= e(asset("js/modules/composer.js")) ?>"></script>
<?php endif; ?>
