<?php
/**
 * views/layouts/admin.php  -  the app shell with the admin menu
 * Receives $content plus optional $title and $pageCss.
 */
$sidebarMode = "admin";
?>
<!doctype html>
<html lang="en">
<head>
    <?php include BASE_PATH . "/views/partials/head.php"; ?>
</head>
<body class="sc-app">
    <?php include BASE_PATH . "/views/partials/sidebar.php"; ?>

    <div class="sc-app__main">
        <?php include BASE_PATH . "/views/partials/navbar-app.php"; ?>
        <main class="sc-app__content">
            <?php include BASE_PATH . "/views/partials/flash.php"; ?>
            <?= $content ?>
        </main>
    </div>

    <?php include BASE_PATH . "/views/partials/scripts.php"; ?>
</body>
</html>
