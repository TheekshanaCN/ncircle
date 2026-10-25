<footer class="sc-footer">
    <div class="container">
        <div class="sc-footer__grid">

            <div class="sc-footer__brand">
                <img class="sc-footer__logo" src="<?= asset(
                    "img/logo.png",
                ) ?>" alt="Ncircle">
                <p class="sc-footer__about">
                    The authoritative academic exchange and peer-learning platform designed for
                    undergraduates across all faculties at NSBM Green University, Sri Lanka.
                </p>
                <div class="sc-footer__notice">
                    <strong>NSBM Student Charter Notice</strong>
                    <p>
                        Ncircle supports cooperative study and ethical learning in adherence to
                        NSBM intellectual property standards and plagiarism guidelines.
                    </p>
                </div>
            </div>

            <nav aria-label="Honor and legal">
                <h2 class="sc-footer__heading">Honor &amp; Legal</h2>
                <ul class="sc-footer__list">
                    <li><a href="#">Academic Integrity</a></li>
                    <li><a href="#">Batch Rep Moderation</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </nav>


            <nav aria-label="Faculties">
                <h2 class="sc-footer__heading">Faculties</h2>
                <ul class="sc-footer__list">
                    <li><a href="#">Faculty of Computing</a></li>
                    <li><a href="#">Faculty of Business</a></li>
                    <li><a href="#">Faculty of Engineering</a></li>
                    <li><a href="#">Faculty of Science</a></li>
                </ul>
            </nav>

            <nav aria-label="Product">
                <h2 class="sc-footer__heading">Product</h2>
                <ul class="sc-footer__list">
                    <li><a href="#">Module Repository</a></li>
                    <li><a href="#">Academic Forum</a></li>
                    <li><a href="#">Revision Pods</a></li>
                    <li><a href="#">Student Vault</a></li>
                </ul>
            </nav>

        </div>

        <div class="sc-footer__bar">
            <p>&copy; <?= date(
                "Y",
            ) ?> Ncircle NSBM. Authorized undergraduate repository. All rights reserved.</p>
            <ul class="sc-footer__meta">
                <li>Faculty of Computing</li>
                <li>Faculty of Engineering</li>
                <li>Faculty of Business</li>
            </ul>
        </div>
    </div>
</footer>
