<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(url('admin/properties.php'));
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    flash_error('Invalid property.');
    redirect(url('admin/properties.php'));
}

// Fetch all image filenames to remove from disk
$imgs = $pdo->prepare("SELECT filename FROM property_images WHERE property_id = ?");
$imgs->execute([$id]);
foreach ($imgs->fetchAll() as $img) {
    @unlink(UPLOAD_DIR . $img['filename']);
}

// Delete the property (cascade removes images + nulls inquiry.property_id)
$pdo->prepare("DELETE FROM properties WHERE id = ?")->execute([$id]);

flash_success('Property deleted.');
redirect(url('admin/properties.php'));