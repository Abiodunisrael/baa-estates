<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page  = 'property-form';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load existing property for edit
$property = null;
$images   = [];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $property = $stmt->fetch();

    if (!$property) {
        flash_error('Property not found.');
        redirect(url('admin/properties.php'));
    }

    $img_stmt = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, sort_order ASC, id ASC");
    $img_stmt->execute([$id]);
    $images = $img_stmt->fetchAll();
}

$admin_title = $property ? 'Edit Property' : 'Add Property';

// Locations for dropdown
$locations = $pdo->query("SELECT id, name FROM locations ORDER BY name")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<form method="post" action="<?= url('admin/property-save.php') ?>" class="admin-form" id="property-form" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $property ? (int)$property['id'] : 0 ?>">

    <div class="admin-card">
        <h2>Basic Information</h2>

        <div class="form-row">
            <div>
                <label class="req">Title</label>
                <input type="text" name="title" required value="<?= e($property['title'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row cols-3">
            <div>
                <label class="req">Property Type</label>
                <select name="property_type" required>
                    <option value="house" <?= ($property['property_type'] ?? '') === 'house' ? 'selected' : '' ?>>House</option>
                    <option value="land"  <?= ($property['property_type'] ?? '') === 'land'  ? 'selected' : '' ?>>Land</option>
                </select>
            </div>
            <div>
                <label class="req">Listing Type</label>
                <select name="listing_type" required>
                    <option value="sale"  <?= ($property['listing_type'] ?? '') === 'sale'  ? 'selected' : '' ?>>For Sale</option>
                    <option value="lease" <?= ($property['listing_type'] ?? '') === 'lease' ? 'selected' : '' ?>>For Lease</option>
                </select>
            </div>
            <div>
                <label class="req">Status</label>
                <select name="status" required>
                    <option value="available" <?= ($property['status'] ?? 'available') === 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="pending"   <?= ($property['status'] ?? '') === 'pending'   ? 'selected' : '' ?>>Pending</option>
                    <option value="sold"      <?= ($property['status'] ?? '') === 'sold'      ? 'selected' : '' ?>>Sold</option>
                    <option value="leased"    <?= ($property['status'] ?? '') === 'leased'    ? 'selected' : '' ?>>Leased</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label class="req">Description</label>
                <textarea name="description" required rows="6"><?= e($property['description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Pricing</h2>

        <div class="form-row cols-2">
            <div>
                <label class="req">Price (₦)</label>
                <input type="number" name="price" required min="0" step="1000" value="<?= e($property['price'] ?? '') ?>">
            </div>
            <div>
                <label>Price Period</label>
                <select name="price_period">
                    <option value="">— None —</option>
                    <option value="total" <?= ($property['price_period'] ?? '') === 'total' ? 'selected' : '' ?>>Total (sale)</option>
                    <option value="month" <?= ($property['price_period'] ?? '') === 'month' ? 'selected' : '' ?>>Per Month</option>
                    <option value="year"  <?= ($property['price_period'] ?? '') === 'year'  ? 'selected' : '' ?>>Per Year</option>
                </select>
                <div class="form-help">Leave "Total" for sales, "Per Year"/"Per Month" for leases.</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Details (optional for land)</h2>

        <div class="form-row cols-3">
            <div>
                <label>Bedrooms</label>
                <input type="number" name="bedrooms" min="0" value="<?= e($property['bedrooms'] ?? '') ?>">
            </div>
            <div>
                <label>Bathrooms</label>
                <input type="number" name="bathrooms" min="0" value="<?= e($property['bathrooms'] ?? '') ?>">
            </div>
            <div>
                <label>Area (sqft)</label>
                <input type="number" name="area_sqft" min="0" value="<?= e($property['area_sqft'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="checkbox-row">
                <input type="checkbox" id="featured" name="featured" value="1" <?= !empty($property['featured']) ? 'checked' : '' ?>>
                <label for="featured" style="margin:0;">Feature this property on the homepage</label>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Location</h2>

        <div class="form-row cols-2">
            <div>
                <label>Area / Neighborhood</label>
                <select name="location_id">
                    <option value="">— None —</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= (int)$loc['id'] ?>" <?= (int)($property['location_id'] ?? 0) === (int)$loc['id'] ? 'selected' : '' ?>>
                            <?= e($loc['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-help">Manage locations in <a href="<?= url('admin/locations.php') ?>">Locations</a>.</div>
            </div>
            <div>
                <label>Full Address</label>
                <input type="text" name="address" value="<?= e($property['address'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row cols-2">
            <div>
                <label>Latitude</label>
                <input type="text" name="latitude" placeholder="e.g. 6.4474" value="<?= e($property['latitude'] ?? '') ?>">
            </div>
            <div>
                <label>Longitude</label>
                <input type="text" name="longitude" placeholder="e.g. 3.4736" value="<?= e($property['longitude'] ?? '') ?>">
            </div>
        </div>
        <div class="form-help">Optional. If both are set, a map shows on the property page.</div>
    </div>

    <div class="admin-card">
        <h2>Images</h2>

        <?php if ($property && count($images) > 0): ?>
            <div class="image-grid" style="margin-bottom:1rem;">
                <?php foreach ($images as $img): ?>
                    <div class="image-item">
                        <?php if ($img['is_primary']): ?>
                            <span class="primary-tag">Primary</span>
                        <?php endif; ?>
                        <img src="<?= e(property_image_url($img['filename'])) ?>" alt="">
                        <div class="image-actions">
                            <?php if (!$img['is_primary']): ?>
                                <button type="submit"
                                        name="set_primary_image"
                                        value="<?= (int)$img['id'] ?>"
                                        formnovalidate
                                        onclick="return confirm('Make this the primary image?');">
                                    Make Primary
                                </button>
                            <?php endif; ?>
                            <button type="submit"
                                    name="delete_image"
                                    value="<?= (int)$img['id'] ?>"
                                    formnovalidate
                                    onclick="return confirm('Delete this image?');">
                                Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-row">
            <div>
                <label>Upload Images</label>
                <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
                <div class="form-help">Max 5 MB per image. JPG, PNG, or WebP. You can select multiple.</div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" name="save_property" value="1" class="btn-admin-primary" id="submit-property">
            <i class="fas fa-save"></i> <?= $property ? 'Update Property' : 'Create Property' ?>
        </button>
        <a href="<?= url('admin/properties.php') ?>" class="btn-admin-ghost">Cancel</a>
        <?php if ($property): ?>
            <a href="<?= url('property/' . e($property['slug'])) ?>" target="_blank" class="btn-admin-ghost" style="margin-left:auto;">
                <i class="fas fa-external-link-alt"></i> View on site
            </a>
        <?php endif; ?>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('property-form');
    const btn  = document.getElementById('submit-property');

    if (!form || !btn) {
        console.error('[property-form] Form or submit button not found');
        return;
    }

    btn.addEventListener('click', (e) => {
        if (e.currentTarget.name !== 'save_property') return;
        setTimeout(() => {
            if (!form.dataset.submitted) {
                console.warn('[property-form] Native submit did not fire — forcing submit()');
                form.submit();
            }
        }, 100);
    });

    form.addEventListener('submit', () => {
        form.dataset.submitted = '1';
    });
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>