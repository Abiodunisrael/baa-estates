<?php
/** Reusable property card. Expects $p (property row with location_name and primary_image). */
$card_url = url('property/' . e($p['slug']));
$badge_class = $p['listing_type'] === 'lease' ? 'badge-lease' : ($p['property_type'] === 'land' ? 'badge-land' : '');
?>
<article class="property-card">
    <a href="<?= $card_url ?>" class="property-thumb">
        <img src="<?= e(property_image_url($p['primary_image'] ?? null)) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
        <span class="property-badge <?= $badge_class ?>">
            <?= e(listing_type_label($p['listing_type'])) ?>
        </span>
        <?php if ($p['status'] !== 'available'): ?>
            <span class="property-status <?= e(status_badge_class($p['status'])) ?>">
                <?= e(ucfirst($p['status'])) ?>
            </span>
        <?php endif; ?>
    </a>
    <div class="property-body">
        <a href="<?= $card_url ?>" class="property-title"><?= e($p['title']) ?></a>
        <div class="property-location">
            <i class="fas fa-map-marker-alt"></i>
            <?= e($p['location_name'] ?? 'Location not specified') ?>
        </div>
        <div class="property-price">
            <?= e(format_price((float)$p['price'], $p['price_period'])) ?>
        </div>
        <div class="property-meta">
            <?php if ($p['property_type'] === 'house'): ?>
                <?php if ($p['bedrooms']): ?><span><i class="fas fa-bed"></i> <?= (int)$p['bedrooms'] ?> Beds</span><?php endif; ?>
                <?php if ($p['bathrooms']): ?><span><i class="fas fa-bath"></i> <?= (int)$p['bathrooms'] ?> Baths</span><?php endif; ?>
            <?php endif; ?>
            <?php if ($p['area_sqft']): ?>
                <span><i class="fas fa-ruler-combined"></i> <?= number_format((int)$p['area_sqft']) ?> sqft</span>
            <?php endif; ?>
        </div>
    </div>
</article>