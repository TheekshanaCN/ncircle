<?php
/**
 * views/partials/head.php  -  shared <head> content for every layout
 * $metaDescription is optional. Link previews never include the query string, so no token can leak.
 * Load order matters: bootstrap -> icons -> theme -> layout -> components -> page
 */
$metaTitle = $title ?? "Ncircle";
$metaDescription = $metaDescription ?? "Ncircle is the study community for NSBM students: share notes, ask questions and join study groups.";
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title><?= e($metaTitle) ?></title>
<meta name="description" content="<?= e($metaDescription) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Ncircle">
<meta property="og:title" content="<?= e($metaTitle) ?>">
<meta property="og:description" content="<?= e($metaDescription) ?>">
<meta property="og:url" content="<?= e(absolute_url(current_path())) ?>">
<meta property="og:image" content="<?= e(absolute_url("/assets/img/favicons/android-chrome-512x512.png")) ?>">
<meta name="twitter:card" content="summary">
<meta name="color-scheme" content="light dark">
<script nonce="<?= e(csp_nonce()) ?>">
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
