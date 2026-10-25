<header class="sc-header">
    <div class="container">
        <nav class="navbar navbar-expand-lg sc-nav" aria-label="Main navigation">
            <a class="sc-nav__brand" href="<?= url('/') ?>">
                <img src="<?= asset('img/logo.png') ?>" alt="Ncircle">
            </a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#scNav"
                    aria-controls="scNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="scNav">
                <ul class="navbar-nav sc-nav__links mx-lg-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>#community">Community</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>#statistics">Statistics</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>#how-it-works">How it works</a></li>
                </ul>

                <div class="sc-nav__actions">
                    <a class="btn btn-outline-primary btn-sm" href="<?= url('/login') ?>">Sign in</a>
                    <a class="btn btn-primary btn-sm" href="<?= url('/register') ?>">Join the Ncircle</a>
                </div>
            </div>
        </nav>
    </div>
</header>
