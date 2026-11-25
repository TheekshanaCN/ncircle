<?php
/**
 * views/layouts/auth.php  -  full-screen dark split page for sign in and sign up
 * Views fill it through the auth-shell component.
 */
?>
<!doctype html>
<html lang="en">
<head>
    <?php include BASE_PATH . "/views/partials/head.php"; ?>
</head>
<body class="sc-auth-page">
    <?= $content ?>
    <?php include BASE_PATH . "/views/partials/scripts.php"; ?>
</body>
</html>
