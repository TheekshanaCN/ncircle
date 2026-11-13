<?php /** components/theme-toggle.php  -  light and dark switch. The icon follows the theme (see theme.css). */ ?>
<button class="sc-round-btn sc-theme-toggle<?= e(isset($class) ? " " . $class : "") ?>" type="button" data-theme-toggle aria-pressed="false" aria-label="Switch to dark theme">
    <i class="bi bi-moon-stars sc-theme-toggle__moon" aria-hidden="true"></i>
    <i class="bi bi-sun sc-theme-toggle__sun" aria-hidden="true"></i>
</button>
