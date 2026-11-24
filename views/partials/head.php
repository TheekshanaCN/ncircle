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
<link rel="icon" href="<?= e(asset("img/favicons/favicon.ico")) ?>" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="<?= e(asset("img/favicons/favicon-32x32.png")) ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?= e(asset("img/favicons/favicon-16x16.png")) ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?= e(asset("img/favicons/apple-touch-icon.png")) ?>">
<link rel="manifest" href="<?= e(asset("img/favicons/site.webmanifest")) ?>">
<meta name="theme-color" content="#ffffff">
<link rel="stylesheet" href="<?= e(asset("vendor/bootstrap/bootstrap.min.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("vendor/bootstrap-icons/font/bootstrap-icons.min.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("css/theme.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("css/layout.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("css/components.css")) ?>">
<link rel="stylesheet" href="<?= e(asset("css/motion.css")) ?>">
<?php if (!empty($pageCss)): ?>
    <link rel="stylesheet" href="<?= e(asset("css/pages/" . $pageCss . ".css")) ?>">
<?php endif; ?>
