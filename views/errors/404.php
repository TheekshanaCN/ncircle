<?php /** views/errors/404.php */ ?>
<div class="sc-error-page">
    <?= component("empty-state", ["icon" => "signpost-split", "title" => "Page not found", "text" => "That link may be old, or the page may have moved.", "actionLabel" => "Back to home", "actionHref" => url("/")]) ?>
    <?php if (isset($debug) && $debug instanceof Throwable): ?>
        <pre class="sc-error-page__debug"><?= e($debug->getMessage() . " in " . $debug->getFile() . ":" . $debug->getLine()) ?></pre>
    <?php endif; ?>
</div>
