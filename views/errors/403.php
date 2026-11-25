<?php /** views/errors/403.php */ ?>
<div class="sc-error-page">
    <?= component("empty-state", ["icon" => "shield-lock", "title" => "You can't open this page", "text" => "This area is for a different kind of account.", "actionLabel" => "Back to home", "actionHref" => url("/")]) ?>
    <?php if (isset($debug) && $debug instanceof Throwable): ?>
        <pre class="sc-error-page__debug"><?= e($debug->getMessage() . " in " . $debug->getFile() . ":" . $debug->getLine()) ?></pre>
    <?php endif; ?>
</div>
