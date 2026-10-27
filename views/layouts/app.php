<?php
/**
 * views/layouts/app.php  -  page shell for LOGGED-IN students (UI lead only)
 * Receives $content plus optional $title and $pageCss.
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? "Ncircle") ?></title>

    <!-- Load order matters: bootstrap -> icons -> theme -> layout -> components -> page -->
    <link rel="stylesheet" href="<?= e(
        asset("vendor/bootstrap/bootstrap.min.css"),
    ) ?>">
    <link rel="stylesheet" href="<?= e(
        asset("vendor/bootstrap-icons/font/bootstrap-icons.min.css"),
    ) ?>">
    <link rel="stylesheet" href="<?= e(asset("css/theme.css")) ?>">
    <link rel="stylesheet" href="<?= e(asset("css/layout.css")) ?>">
    <link rel="stylesheet" href="<?= e(asset("css/components.css")) ?>">
    <?php if (!empty($pageCss)): ?>
        <link rel="stylesheet" href="<?= e(
            asset("css/pages/" . $pageCss . ".css"),
        ) ?>">
    <?php endif; ?>
</head>
<body>
    <?php include BASE_PATH . "/views/partials/navbar-app.php"; ?>

    <main class="sc-main">
        <div class="container">
            <?= $content ?>
        </div>
    </main>

    <?php include BASE_PATH . "/views/partials/footer.php"; ?>

    <script src="<?= e(
        asset("vendor/bootstrap/bootstrap.bundle.min.js"),
    ) ?>"></script>
    <script src="<?= e(asset("js/app.js")) ?>"></script>
</body>
</html>
