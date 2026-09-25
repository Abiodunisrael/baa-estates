<?php
/** Home page. */

$page_title = 'Home';
$page_description = 'Find houses and lands for sale or lease across Nigeria.';

// Featured first, then latest to fill up to 6
$stmt = $pdo->query("
    SELECT p.*, l.name AS location_name,
           (SELECT filename FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS primary_image
    FROM properties p
    LEFT JOIN locations l ON p.location_id = l.id
    WHERE p.status = 'available'
    ORDER BY p.featured DESC, p.created_at DESC
    LIMIT 6
");
$featured = $stmt->fetchAll();

// Locations for search dropdown
$locations = $pdo->query("SELECT id, name FROM locations ORDER BY name")->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="container hero-inner">
        <h1>Find Your <span>Perfect</span> Home or Land</h1>
        <p>Discover premium houses, apartments, and lands for sale or lease across Nigeria's most desirable locations.</p>

        <form class="search-bar" method="get" action="<?= url('properties') ?>">
            <select name="listing">
                <option value="">Any Listing</option>
                <option value="sale">For Sale</option>
                <option value="lease">For Lease</option>
            </select>
            <select name="type">
                <option value="">Any Type</option>
                <option value="house">House</option>
                <option value="land">Land</option>
            </select>
            <select name="location">
                <option value="">Any Location</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= (int)$loc['id'] ?>"><?= e($loc['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-accent">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>
</section>

<!-- FEATURED -->
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2 class="section-title" style="text-align:left;">Featured Properties</h2>
                <p class="section-subtitle" style="text-align:left; margin:0;">Handpicked listings from our portfolio</p>
            </div>
            <a href="<?= url('properties') ?>" class="btn btn-outline">View All <i class="fas fa-arrow-right"></i></a>
        </div>

        <?php if (count($featured) > 0): ?>
            <div class="property-grid">
                <?php foreach ($featured as $p): ?>
                    <?php require __DIR__ . '/../includes/property-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding:3rem 1rem; color: var(--color-muted);">
                <i class="fas fa-home" style="font-size:3rem; color: var(--color-border);"></i>
                <p class="mt-2">No properties published yet. Check back soon.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- WHY US -->
<section class="section" style="background: var(--color-surface); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
    <div class="container">
        <h2 class="section-title">Why Choose BAA Estates</h2>
        <p class="section-subtitle">Trusted by families and investors across Nigeria</p>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Verified Listings</h3>
                <p>Every property is personally vetted by our team to ensure authenticity and clean documentation.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-handshake"></i></div>
                <h3>Trusted Agents</h3>
                <p>Years of experience helping families and businesses find the right property at the right price.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <h3>Dedicated Support</h3>
                <p>From first viewing to final paperwork, our team supports you at every step of the journey.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section">
    <div class="container text-center">
        <h2>Can't find what you're looking for?</h2>
        <p class="section-subtitle">Tell us what you need and we'll find it for you.</p>
        <a href="<?= url('contact') ?>" class="btn btn-primary">
            <i class="fas fa-envelope"></i> Contact Us
        </a>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>