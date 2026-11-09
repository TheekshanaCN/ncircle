<?php /** views/errors/500.php */ ?>
<div class="sc-error-page">
    <?= component("empty-state", ["icon" => "exclamation-triangle", "title" => "Something went wrong", "text" => "It's not you. Please try again in a moment.", "actionLabel" => "Back to home", "actionHref" => url("/")]) ?>
    <?php if (isset($debug) && $debug instanceof Throwable): ?>
        <pre class="sc-error-page__debug"><?= e($debug->getMessage() . " in " . $debug->getFile() . ":" . $debug->getLine()) ?></pre>
    <?php endif; ?>
</div>
