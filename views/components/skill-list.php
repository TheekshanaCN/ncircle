<?php
/**
 * components/skill-list.php  -  comma-separated skills as chips
 *   <?= component('skill-list', ['skills' => 'Python, Statistics', 'open' => true]) ?>
 * open = true adds an "Open to help" pill.
 */
$list = array_filter(array_map('trim', explode(',', (string) ($skills ?? ''))), fn ($s) => $s !== '');
$open = !empty($open);
?>
<?php if ($list || $open): ?>
    <div class="sc-skills">
        <?php if ($open): ?><span class="sc-state sc-state--lime">OPEN TO HELP</span><?php endif; ?>
        <?php foreach ($list as $skill): ?><span class="sc-chip"><?= e($skill) ?></span><?php endforeach; ?>
    </div>
<?php endif; ?>
