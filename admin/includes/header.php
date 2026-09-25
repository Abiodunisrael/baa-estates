<?php
$admin_page  = $admin_page  ?? 'dashboard';
$admin_title = $admin_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($admin_title) ?> — Admin | <?= e(site_name()) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= url('assets/images/logo.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('admin/assets/admin.css') ?>">
</head>
<body class="admin-body">

<aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-brand">
        <img src="<?= url('assets/images/logo.svg') ?>" alt="" width="36" height="36">
        <div>
            <strong>BAA Admin</strong>
            <small>Control Panel</small>
        </div>
    </div>

    <nav class="admin-nav">
        <a href="<?= url('admin/index.php') ?>" class="<?= $admin_page === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="<?= url('admin/properties.php') ?>" class="<?= $admin_page === 'properties' ? 'active' : '' ?>">
            <i class="fas fa-building"></i> Properties
        </a>
        <a href="<?= url('admin/property-form.php') ?>" class="<?= $admin_page === 'property-form' ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i> Add Property
        </a>
        <a href="<?= url('admin/locations.php') ?>" class="<?= $admin_page === 'locations' ? 'active' : '' ?>">
            <i class="fas fa-map-marker-alt"></i> Locations
        </a>
        <a href="<?= url('admin/inquiries.php') ?>" class="<?= $admin_page === 'inquiries' ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i> Inquiries
            <?php
            $new_count = (int)$pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();
            if ($new_count > 0):
            ?>
                <span class="nav-badge"><?= $new_count ?></span>
            <?php endif; ?>
        </a>

        <div class="admin-nav-divider"></div>

        <a href="<?= url('admin/settings.php') ?>" class="<?= $admin_page === 'settings' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i> Site Settings
        </a>
        <a href="<?= url('admin/change-password.php') ?>" class="<?= $admin_page === 'change-password' ? 'active' : '' ?>">
            <i class="fas fa-key"></i> Change Password
        </a>
        <a href="<?= url('admin/logout.php') ?>" class="nav-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="<?= url('/') ?>" target="_blank" class="view-site-link">
            <i class="fas fa-external-link-alt"></i> View Site
        </a>
    </div>
</aside>

<div class="admin-main">
    <header class="admin-topbar">
        <button type="button" class="admin-menu-toggle" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="admin-page-title"><?= e($admin_title) ?></h1>
        <div class="admin-user">
            <i class="fas fa-user-circle"></i>
            <span><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
        </div>
    </header>

    <?php
    $flashes = flash_get();
    if (!empty($flashes)):
    ?>
    <div class="admin-flash-stack">
        <?php foreach ($flashes as $f): ?>
            <div class="flash flash-<?= e($f['type']) ?>">
                <i class="fas <?= $f['type'] === 'success' ? 'fa-check-circle' : ($f['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle') ?>"></i>
                <span><?= e($f['message']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="admin-content">