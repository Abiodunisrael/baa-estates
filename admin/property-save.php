<?php
require_once __DIR__ . '/includes/auth.php';

// ============================================================
// IMAGE ACTIONS (button names on the main form)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Delete image
    if (!empty($_POST['delete_image'])) {
        $image_id    = (int)$_POST['delete_image'];
        $property_id = (int)($_POST['id'] ?? 0);

        $s = $pdo->prepare("SELECT * FROM property_images WHERE id = ? AND property_id = ?");
        $s->execute([$image_id, $property_id]);
        $img = $s->fetch();

        if ($img) {
            @unlink(UPLOAD_DIR . $img['filename']);
            $pdo->prepare("DELETE FROM property_images WHERE id = ?")->execute([$image_id]);
            flash_success('Image deleted.');
        } else {
            flash_error('Image not found.');
        }
        redirect(url('admin/property-form.php?id=' . $property_id));
    }

    // Set primary image
    if (!empty($_POST['set_primary_image'])) {
        $image_id    = (int)$_POST['set_primary_image'];
        $property_id = (int)($_POST['id'] ?? 0);

        $pdo->prepare("UPDATE property_images SET is_primary = 0 WHERE property_id = ?")->execute([$property_id]);
        $pdo->prepare("UPDATE property_images SET is_primary = 1 WHERE id = ? AND property_id = ?")->execute([$image_id, $property_id]);

        flash_success('Primary image updated.');
        redirect(url('admin/property-form.php?id=' . $property_id));
    }
}

// ============================================================
// MAIN PROPERTY SAVE (insert or update)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(url('admin/properties.php'));
}

$id = (int)($_POST['id'] ?? 0);

// Collect + sanitize
$title         = trim($_POST['title'] ?? '');
$description   = trim($_POST['description'] ?? '');
$property_type = $_POST['property_type'] ?? 'house';
$listing_type  = $_POST['listing_type'] ?? 'sale';
$status        = $_POST['status'] ?? 'available';
$price         = (float)($_POST['price'] ?? 0);
$price_period  = $_POST['price_period'] ?? null;
$bedrooms      = ($_POST['bedrooms']  ?? '') !== '' ? (int)$_POST['bedrooms']  : null;
$bathrooms     = ($_POST['bathrooms'] ?? '') !== '' ? (int)$_POST['bathrooms'] : null;
$area_sqft     = ($_POST['area_sqft'] ?? '') !== '' ? (int)$_POST['area_sqft'] : null;
$location_id   = ($_POST['location_id'] ?? '') !== '' ? (int)$_POST['location_id'] : null;
$address       = trim($_POST['address'] ?? '');
$latitude      = ($_POST['latitude']  ?? '') !== '' ? (float)$_POST['latitude']  : null;
$longitude     = ($_POST['longitude'] ?? '') !== '' ? (float)$_POST['longitude'] : null;
$featured      = !empty($_POST['featured']) ? 1 : 0;

// Validate
$errors = [];
if ($title === '')       $errors[] = 'Title is required.';
if ($description === '') $errors[] = 'Description is required.';
if ($price <= 0)         $errors[] = 'Price must be greater than 0.';
if (!in_array($property_type, ['house','land'], true))      $errors[] = 'Invalid property type.';
if (!in_array($listing_type, ['sale','lease'], true))       $errors[] = 'Invalid listing type.';
if (!in_array($status, ['available','pending','sold','leased'], true)) $errors[] = 'Invalid status.';

if ($price_period !== null && !in_array($price_period, ['','total','month','year'], true)) {
    $price_period = null;
}
if ($price_period === '') $price_period = null;

// Build a unique slug
$base_slug = slugify($title);
if ($base_slug === '') $base_slug = 'property';
$slug = $base_slug;
$n = 2;
while (true) {
    if ($id > 0) {
        $s = $pdo->prepare("SELECT id FROM properties WHERE slug = ? AND id != ?");
        $s->execute([$slug, $id]);
    } else {
        $s = $pdo->prepare("SELECT id FROM properties WHERE slug = ?");
        $s->execute([$slug]);
    }
    if (!$s->fetch()) break;
    $slug = $base_slug . '-' . $n++;
}

// If errors, bounce back with messages
if (!empty($errors)) {
    foreach ($errors as $e) flash_error($e);
    redirect(url('admin/property-form.php' . ($id ? '?id=' . $id : '')));
}

// ============================================================
// Save (insert or update)
// ============================================================
if ($id > 0) {
    $sql = "UPDATE properties SET
                title = ?, slug = ?, description = ?, property_type = ?, listing_type = ?,
                status = ?, price = ?, price_period = ?, bedrooms = ?, bathrooms = ?,
                area_sqft = ?, location_id = ?, address = ?, latitude = ?, longitude = ?,
                featured = ?
            WHERE id = ?";
    $params = [
        $title, $slug, $description, $property_type, $listing_type,
        $status, $price, $price_period, $bedrooms, $bathrooms,
        $area_sqft, $location_id, $address, $latitude, $longitude,
        $featured, $id
    ];
    $pdo->prepare($sql)->execute($params);
    flash_success('Property updated.');
} else {
    $sql = "INSERT INTO properties
                (title, slug, description, property_type, listing_type, status,
                 price, price_period, bedrooms, bathrooms, area_sqft,
                 location_id, address, latitude, longitude, featured)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [
        $title, $slug, $description, $property_type, $listing_type, $status,
        $price, $price_period, $bedrooms, $bathrooms, $area_sqft,
        $location_id, $address, $latitude, $longitude, $featured
    ];
    $pdo->prepare($sql)->execute($params);
    $id = (int)$pdo->lastInsertId();
    flash_success('Property created.');
}

// ============================================================
// Handle image uploads
// ============================================================
if (!empty($_FILES['images']['name'][0])) {

    if (!is_dir(UPLOAD_DIR)) {
        @mkdir(UPLOAD_DIR, 0755, true);
    }

    $allowed   = ALLOWED_IMAGE_TYPES;
    $uploaded  = 0;
    $failed    = 0;

    foreach ($_FILES['images']['name'] as $i => $name) {

        if (empty($_FILES['images']['name'][$i])) continue;

        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
            $failed++;
            continue;
        }

        if ($_FILES['images']['size'][$i] > MAX_UPLOAD_BYTES) {
            $failed++;
            continue;
        }

        $tmp  = $_FILES['images']['tmp_name'][$i];
        $mime = @mime_content_type($tmp);

        if (!in_array($mime, $allowed, true)) {
            $failed++;
            continue;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $filename = 'prop-' . $id . '-' . bin2hex(random_bytes(6)) . '.' . $ext;

        if (move_uploaded_file($tmp, UPLOAD_DIR . $filename)) {

            $check = $pdo->prepare("SELECT COUNT(*) FROM property_images WHERE property_id = ? AND is_primary = 1");
            $check->execute([$id]);
            $is_primary = ((int)$check->fetchColumn() === 0) ? 1 : 0;

            $pdo->prepare("INSERT INTO property_images (property_id, filename, is_primary) VALUES (?, ?, ?)")
                ->execute([$id, $filename, $is_primary]);

            $uploaded++;
        } else {
            $failed++;
        }
    }

    if ($uploaded > 0) {
        flash_success($uploaded . ' image' . ($uploaded === 1 ? '' : 's') . ' uploaded.');
    }
    if ($failed > 0) {
        flash_error($failed . ' image' . ($failed === 1 ? '' : 's') . ' failed (size, format, or upload error).');
    }
}

// ============================================================
// Redirect back to the edit form
// ============================================================
redirect(url('admin/property-form.php?id=' . $id));