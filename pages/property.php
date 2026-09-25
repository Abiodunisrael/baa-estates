<?php
/** Single property detail page. Expects $param = slug. */

if (!$param) {
    redirect(url('properties'));
}

$stmt = $pdo->prepare("
    SELECT p.*, l.name AS location_name, l.slug AS location_slug, l.state AS location_state, l.description AS location_description
    FROM properties p
    LEFT JOIN locations l ON p.location_id = l.id
    WHERE p.slug = ?
    LIMIT 1
");
$stmt->execute([$param]);
$property = $stmt->fetch();

if (!$property) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return;
}

$page_title = $property['title'];
$page_description = excerpt($property['description'], 160);

// Images
$img_stmt = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, sort_order ASC, id ASC");
$img_stmt->execute([$property['id']]);
$images = $img_stmt->fetchAll();

if (empty($images)) {
    $images = [['filename' => null]];
}

// Inquiry submission
$form_errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'inquiry') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '')                                  $form_errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $form_errors[] = 'Please enter a valid email address.';
    if ($message === '')                               $form_errors[] = 'Please enter a message.';

    if (empty($form_errors)) {
        $ins = $pdo->prepare("INSERT INTO inquiries (property_id, name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$property['id'], $name, $email, $phone ?: null, $message]);
        flash_success('Thank you! Your inquiry has been sent. We will get back to you shortly.');
        redirect(url('property/' . $property['slug']));
    }
}

require __DIR__ . '/../includes/header.php';
?>

<section class="property-detail">
    <div class="container">

        <nav class="breadcrumb">
            <a href="<?= url('/') ?>">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="<?= url('properties') ?>">Properties</a>
            <i class="fas fa-chevron-right"></i>
            <span><?= e($property['title']) ?></span>
        </nav>

        <div class="detail-layout">

            <div class="detail-main">
                <div class="gallery">
                    <div class="gallery-main">
                        <img id="gallery-main-img"
                             src="<?= e(property_image_url($images[0]['filename'] ?? null)) ?>"
                             alt="<?= e($property['title']) ?>">
                        <?php if ($property['status'] !== 'available'): ?>
                            <span class="property-status <?= e(status_badge_class($property['status'])) ?>" style="top:1rem; right:1rem; font-size:.8rem;">
                                <?= e(ucfirst($property['status'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if (count($images) > 1): ?>
                        <div class="gallery-thumbs">
                            <?php foreach ($images as $i => $img): ?>
                                <button type="button"
                                        class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>"
                                        data-src="<?= e(property_image_url($img['filename'])) ?>">
                                    <img src="<?= e(property_image_url($img['filename'])) ?>" alt="">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="detail-header">
                    <div class="detail-badges">
                        <span class="property-badge <?= $property['listing_type'] === 'lease' ? 'badge-lease' : ($property['property_type'] === 'land' ? 'badge-land' : '') ?>">
                            <?= e(listing_type_label($property['listing_type'])) ?>
                        </span>
                        <span class="property-badge" style="background:var(--color-primary);">
                            <?= e(property_type_label($property['property_type'])) ?>
                        </span>
                    </div>
                    <h1><?= e($property['title']) ?></h1>
                    <p class="detail-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= e($property['address'] ?: $property['location_name'] ?: 'Location not specified') ?>
                        <?php if ($property['location_state']): ?>
                            , <?= e($property['location_state']) ?>
                        <?php endif; ?>
                    </p>
                    <div class="detail-price">
                        <?= e(format_price((float)$property['price'], $property['price_period'])) ?>
                    </div>
                </div>

                <div class="detail-facts">
                    <?php if ($property['property_type'] === 'house'): ?>
                        <?php if ($property['bedrooms']): ?>
                            <div class="fact"><i class="fas fa-bed"></i><span><strong><?= (int)$property['bedrooms'] ?></strong> Bedrooms</span></div>
                        <?php endif; ?>
                        <?php if ($property['bathrooms']): ?>
                            <div class="fact"><i class="fas fa-bath"></i><span><strong><?= (int)$property['bathrooms'] ?></strong> Bathrooms</span></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($property['area_sqft']): ?>
                        <div class="fact"><i class="fas fa-ruler-combined"></i><span><strong><?= number_format((int)$property['area_sqft']) ?></strong> sqft</span></div>
                    <?php endif; ?>
                    <div class="fact"><i class="fas fa-calendar-alt"></i><span>Listed <strong><?= date('M j, Y', strtotime($property['created_at'])) ?></strong></span></div>
                </div>

                <div class="detail-section">
                    <h2>Description</h2>
                    <div class="prose"><?= nl2br(e($property['description'])) ?></div>
                </div>

                <?php if ($property['location_name']): ?>
                <div class="detail-section">
                    <h2>Location</h2>
                    <h3 style="font-family:var(--font-body); font-size:1rem; color:var(--color-primary); margin-bottom:.5rem;">
                        <i class="fas fa-map-marked-alt"></i> <?= e($property['location_name']) ?>
                        <?php if ($property['location_state']): ?>, <?= e($property['location_state']) ?><?php endif; ?>
                    </h3>
                    <?php if ($property['location_description']): ?>
                        <p class="prose"><?= nl2br(e($property['location_description'])) ?></p>
                    <?php endif; ?>
                    <?php if ($property['address']): ?>
                        <p><strong>Address:</strong> <?= e($property['address']) ?></p>
                    <?php endif; ?>
                    <?php if ($property['latitude'] && $property['longitude']): ?>
                        <div class="map-embed">
                            <iframe
                                src="https://www.openstreetmap.org/export/embed.html?bbox=<?= (float)$property['longitude'] - 0.01 ?>%2C<?= (float)$property['latitude'] - 0.01 ?>%2C<?= (float)$property['longitude'] + 0.01 ?>%2C<?= (float)$property['latitude'] + 0.01 ?>&layer=mapnik&marker=<?= (float)$property['latitude'] ?>%2C<?= (float)$property['longitude'] ?>"
                                loading="lazy"></iframe>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <aside class="detail-sidebar">
                <div class="inquiry-card">
                    <h3><i class="fas fa-envelope"></i> Interested?</h3>
                    <p class="inquiry-sub">Send us a message about this property and we'll get back to you.</p>

                    <?php if (!empty($form_errors)): ?>
                        <div class="form-errors">
                            <?php foreach ($form_errors as $err): ?>
                                <p><i class="fas fa-exclamation-circle"></i> <?= e($err) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="inquiry-form">
                        <input type="hidden" name="form" value="inquiry">

                        <label>
                            <span>Your Name *</span>
                            <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
                        </label>
                        <label>
                            <span>Email *</span>
                            <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
                        </label>
                        <label>
                            <span>Phone</span>
                            <input type="tel" name="phone" value="<?= e($_POST['phone'] ?? '') ?>">
                        </label>
                        <label>
                            <span>Message *</span>
                            <textarea name="message" rows="4" required><?= e($_POST['message'] ?? "I'm interested in \"" . $property['title'] . "\". Please contact me.") ?></textarea>
                        </label>

                        <button type="submit" class="btn btn-accent btn-block">
                            <i class="fas fa-paper-plane"></i> Send Inquiry
                        </button>
                    </form>

                    <div class="inquiry-contact">
                        <p><i class="fas fa-phone"></i> <?= e(site_phone()) ?></p>
                        <p><i class="fas fa-envelope"></i> <?= e(site_email()) ?></p>
                    </div>
                </div>
            </aside>

        </div>

        <?php
        $rel_stmt = $pdo->prepare("
            SELECT p.*, l.name AS location_name,
                   (SELECT filename FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS primary_image
            FROM properties p
            LEFT JOIN locations l ON p.location_id = l.id
            WHERE p.id != ? AND p.status = 'available' AND (p.property_type = ? OR p.listing_type = ?)
            ORDER BY RAND()
            LIMIT 3
        ");
        $rel_stmt->execute([$property['id'], $property['property_type'], $property['listing_type']]);
        $related = $rel_stmt->fetchAll();
        ?>
        <?php if (count($related) > 0): ?>
            <section class="section" style="padding-top:3rem;">
                <h2 class="section-title" style="text-align:left;">Similar Properties</h2>
                <p class="section-subtitle" style="text-align:left;">You may also like these</p>
                <div class="property-grid">
                    <?php foreach ($related as $p): ?>
                        <?php require __DIR__ . '/../includes/property-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

    </div>
</section>

<script>
document.querySelectorAll('.gallery-thumb').forEach(thumb => {
    thumb.addEventListener('click', () => {
        const src = thumb.dataset.src;
        document.getElementById('gallery-main-img').src = src;
        document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    });
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>