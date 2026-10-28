<?php
/**
 * views/home/sections/hero.php  -  landing page section 1: hero ( Theekshana )
 */
?>
<section class="landing-hero" id="top" aria-labelledby="hero-title">
    <div class="landing-hero__inner">

        <div class="landing-hero__copy">
            <h1 class="landing-hero__title" id="hero-title">
                Study Smarter,
                <span class="sc-script">Together.</span>
            </h1>

            <p class="landing-hero__lead">
                Curated peer intelligence mapped directly to official NSBM module specifications:
                <span class="sc-code">CS204</span>,
                <span class="sc-code">SE301</span>, and
                <span class="sc-code">BM102</span>.
                Access lecture notes, marking rubrics, and senior study archives.
            </p>

            <div class="landing-hero__actions">
                <?= component("button", [
                    "label" => "Join the Ncircle",
                    "href" => url("/register"),
                    "icon" => "arrow-right",
                    "iconPosition" => "end",
                ]) ?>
                <?= component("button", [
                    "label" => "Sign in",
                    "href" => url("/login"),
                    "variant" => "light",
                ]) ?>
            </div>

            <div class="landing-hero__trust">
                <div class="sc-avatars" aria-hidden="true">
                    <span class="sc-avatar">NL</span>
                    <span class="sc-avatar sc-avatar--lime">KS</span>
                    <span class="sc-avatar sc-avatar--lime-light">TW</span>
                    <span class="sc-avatar sc-avatar--white">RL</span>
                    <span class="sc-avatar sc-avatar--lime sc-avatar--count">+1.2k</span>
                </div>
                <p><strong>1,200+ NSBM undergraduates</strong> across 3 faculties collaborating this semester</p>
            </div>
        </div>

        <div class="landing-hero__visual">
            <img class="landing-hero__art" src="<?= e(
                asset("img/landing-page-headr.png"),
            ) ?>" width="553" height="553" alt="Landing Page header Imagee">
        </div>

    </div>
</section>
