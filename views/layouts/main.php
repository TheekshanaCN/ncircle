<?php
/**
 * views/layouts/main.php  -  page shell for the student-facing site (UI lead only)
 * Receives $content (the page HTML) plus optional $title and $pageCss.
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? "Ncircle") ?></title>

    <!-- Load order matters: bootstrap -> theme -> layout -> components -> page -->
    <link rel="stylesheet" href="<?= asset(
        "vendor/bootstrap/bootstrap.min.css",
    ) ?>">
    <link rel="stylesheet" href="<?= asset("css/theme.css") ?>">
    <link rel="stylesheet" href="<?= asset("css/layout.css") ?>">
    <link rel="stylesheet" href="<?= asset("css/components.css") ?>">
    <?php if (!empty($pageCss)): ?>
        <link rel="stylesheet" href="<?= asset(
            "css/pages/" . e($pageCss) . ".css",
        ) ?>">
    <?php endif; ?>
</head>
<body>
    <?php include BASE_PATH . "/views/partials/navbar-public.php"; ?>

    <main class="sc-main">
        <div class="container">
            <?= $content ?>
        </div>
    </main>

    <?php include BASE_PATH . "/views/partials/footer.php"; ?>
</body>
</html>
