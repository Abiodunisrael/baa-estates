<?php
$page_title = 'About Us';
$page_description = 'Learn about ' . site_name() . ' — trusted real estate partner in Nigeria.';
require __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>About <?= e(site_name()) ?></h1>
        <p>Your trusted real estate partner in Nigeria</p>
    </div>
</section>

<section class="section">
    <div class="container about-grid">
        <div class="about-text">
            <h2>Who We Are</h2>
            <p>
                <?= e(site_name()) ?> is a full-service real estate company dedicated to connecting
                families, investors, and businesses with quality properties across Nigeria.
                Whether you're buying your first home, leasing an apartment, or investing in land,
                we're here to make the process simple, transparent, and rewarding.
            </p>
            <p>
                We combine local knowledge with a commitment to professionalism. Every listing on
                our platform is personally vetted — we don't just advertise properties, we stand
                behind them.
            </p>

            <h2 class="mt-3">Our Mission</h2>
            <p>
                To make property ownership accessible and stress-free for everyone, by providing
                verified listings, honest advice, and end-to-end support from first viewing to
                final paperwork.
            </p>

            <div class="about-stats">
                <div><strong>500+</strong><span>Properties Listed</span></div>
                <div><strong>10+</strong><span>Years Experience</span></div>
                <div><strong>1000+</strong><span>Happy Clients</span></div>
            </div>
        </div>

        <div class="about-side">
            <div class="info-card">
                <i class="fas fa-map-marker-alt"></i>
                <h4>Visit Us</h4>
                <p><?= e(site_address()) ?></p>
            </div>
            <div class="info-card">
                <i class="fas fa-phone"></i>
                <h4>Call Us</h4>
                <p><?= e(site_phone()) ?></p>
            </div>
            <div class="info-card">
                <i class="fas fa-envelope"></i>
                <h4>Email Us</h4>
                <p><?= e(site_email()) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--color-surface); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
    <div class="container">
        <h2 class="section-title">Our Values</h2>
        <p class="section-subtitle">What drives us every day</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-balance-scale"></i></div>
                <h3>Integrity</h3>
                <p>Honest pricing, clear terms, and no hidden fees — ever.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-award"></i></div>
                <h3>Quality</h3>
                <p>We only list properties we would be proud to own ourselves.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <h3>Client First</h3>
                <p>Your goals come before our commission — always.</p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>