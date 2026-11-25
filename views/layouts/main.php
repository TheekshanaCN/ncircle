<?php
/**
 * views/layouts/main.php  -  page shell for the public site
 * Receives $content (the page HTML) plus optional $title and $pageCss.
 */
?>
<!doctype html>
<html lang="en">
<head>
    <?php include BASE_PATH . "/views/partials/head.php"; ?>
</head>
<body>
    <?php include BASE_PATH . "/views/partials/navbar-public.php"; ?>

    <main class="sc-main">
        <div class="container">
            <?php include BASE_PATH . "/views/partials/flash.php"; ?>
            <?= $content ?>
        </div>
    </main>

    <?php include BASE_PATH . "/views/partials/footer.php"; ?>
    <?php include BASE_PATH . "/views/partials/scripts.php"; ?>
</body>
</html>
