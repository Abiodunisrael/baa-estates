<?php
/** Shared header — opened by every public page. */
$page_title = $page_title ?? site_name();
$page_description = $page_description ?? site_tagline();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($page_description) ?>">
    <title><?= e($page_title) ?> — <?= e(site_name()) ?></title>

    <link rel="icon" type="image/svg+xml" href="<?= url('assets/images/logo.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>

<?php require __DIR__ . '/nav.php'; ?>

<?php
// Flash messages
$flashes = flash_get();
if (!empty($flashes)):
?>
<div class="flash-container">
    <?php foreach ($flashes as $f): ?>
        <div class="flash flash-<?= e($f['type']) ?>">
            <i class="fas <?= $f['type'] === 'success' ? 'fa-check-circle' : ($f['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle') ?>"></i>
            <span><?= e($f['message']) ?></span>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<main class="site-main">