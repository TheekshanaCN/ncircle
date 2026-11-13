<?php
/**
 * views/partials/head.php  -  shared <head> content for every layout
 * Load order matters: bootstrap -> icons -> theme -> layout -> components -> page
 */
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title><?= e($title ?? "Ncircle") ?></title>
<meta name="color-scheme" content="light dark">
<script>
    /* Before first paint, so there is no flash: saved choice first, otherwise the device setting. */
    (function (r) {
        var theme = "light";
        try {
            theme = localStorage.getItem("sc-theme") || (matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light");
            if (localStorage.getItem("sc-sidebar") === "collapsed") r.dataset.sidebar = "collapsed";
        } catch (e) {}
        r.dataset.theme = theme;
        r.setAttribute("data-bs-theme", theme);
    })(document.documentElement);
</script>
<link rel="icon" href="<?= e(asset("img/logo.png")) ?>">
<link rel="stylesheet" href="<?= e(asset("vendor/bootstrap/bootstrap.min.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("vendor/bootstrap-icons/font/bootstrap-icons.min.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("css/theme.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("css/layout.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("css/components.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("css/motion.css")) ?>">
<?php if (!empty($pageCss)): ?>
    <link rel="stylesheet" href="<?= e(asset("css/pages/" . $pageCss . ".css")) ?>">
<?php endif; ?>
