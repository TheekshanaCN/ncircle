<?php
/**
 * views/home/sections/hero.php  -  landing page section 1: hero ( Theekshana )
 */
?>
<section class="landing-hero" id="top" aria-labelledby="hero-title" data-spotlight data-parallax>
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
                    <span class="sc-avatar"><i class="bi bi-person-fill"></i></span>
                    <span class="sc-avatar sc-avatar--lime"><i class="bi bi-person-fill"></i></span>
                    <span class="sc-avatar sc-avatar--lime-light"><i class="bi bi-person-fill"></i></span>
                    <span class="sc-avatar sc-avatar--white"><i class="bi bi-person-fill"></i></span>
                </div>
                <p><strong><span data-live-value="students" data-count="<?= (int) $live["values"]["students"] ?>"><?= number_format($live["values"]["students"]) ?></span> NSBM undergraduates</strong> across <span data-live-value="faculties"><?= (int) $live["values"]["faculties"] ?></span> <span data-live-text="facultyWord"><?= e($live["texts"]["facultyWord"]) ?></span> collaborating on Ncircle</p>
            </div>
        </div>

        <div class="landing-hero__visual">
            <img class="landing-hero__art" src="<?= e(
                asset("img/landing-page-headr.webp"),
            ) ?>" width="553" height="553" alt="Four NSBM students standing together with their books">
        </div>

    </div>
</section>
