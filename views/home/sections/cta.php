<?php /** views/home/sections/cta.php  -  landing page section 6: closing banner */ ?>
<section class="landing-cta" aria-labelledby="cta-title" data-spotlight>
    <span class="sc-glow landing-cta__glow landing-cta__glow--a" aria-hidden="true"></span>
    <span class="sc-glow landing-cta__glow landing-cta__glow--b" aria-hidden="true"></span>
    <h2 class="landing-cta__title" id="cta-title">Ready to <span class="sc-script">join the Ncircle?</span></h2>
    <p class="landing-cta__lead">Create your account with your NSBM university email or student ID and get instant access to <span data-live-value="notes"><?= number_format($live["values"]["notes"]) ?></span> shared notes today.</p>
    <div class="landing-cta__actions">
        <?= component("button", ["label" => "Join the Ncircle", "href" => url("/register"), "icon" => "arrow-right", "iconPosition" => "end"]) ?>
        <?= component("button", ["label" => "Sign in", "href" => url("/login"), "variant" => "light"]) ?>
    </div>
</section>
