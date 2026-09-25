</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-col">
            <h4><?= e(site_name()) ?></h4>
            <p><?= e(site_tagline()) ?>. Trusted real estate partner for homes, lands, and commercial property.</p>
            <div class="socials">
                <?php if (setting('social_facebook')): ?>
                    <a href="<?= e(setting('social_facebook')) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if (setting('social_instagram')): ?>
                    <a href="<?= e(setting('social_instagram')) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if (setting('social_twitter')): ?>
                    <a href="<?= e(setting('social_twitter')) ?>" target="_blank" rel="noopener" aria-label="X"><i class="fab fa-x-twitter"></i></a>
                <?php endif; ?>
                <?php if (setting('social_linkedin')): ?>
                    <a href="<?= e(setting('social_linkedin')) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?= url('properties?listing=sale') ?>">Properties for Sale</a></li>
                <li><a href="<?= url('properties?listing=lease') ?>">Properties for Lease</a></li>
                <li><a href="<?= url('properties?type=land') ?>">Lands</a></li>
                <li><a href="<?= url('about') ?>">About Us</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Contact</h4>
            <ul class="contact-list">
                <li><i class="fas fa-map-marker-alt"></i> <?= e(site_address()) ?></li>
                <li><i class="fas fa-phone"></i> <?= e(site_phone()) ?></li>
                <li><i class="fas fa-envelope"></i> <?= e(site_email()) ?></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= e(site_name()) ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="<?= url('assets/js/main.js') ?>"></script>

<?php require __DIR__ . '/whatsapp-button.php'; ?>

</body>
</html>
</body>
</html>