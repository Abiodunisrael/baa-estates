<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page  = 'dashboard';
$admin_title = 'Dashboard';

// Stats
$total_properties = (int)$pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$total_available  = (int)$pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'available'")->fetchColumn();
$total_sold       = (int)$pdo->query("SELECT COUNT(*) FROM properties WHERE status IN ('sold','leased')")->fetchColumn();
$total_inquiries  = (int)$pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$new_inquiries    = (int)$pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();

// Recent properties
$recent = $pdo->query("
    SELECT p.*, l.name AS location_name,
           (SELECT filename FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS primary_image
    FROM properties p
    LEFT JOIN locations l ON p.location_id = l.id
    ORDER BY p.created_at DESC
    LIMIT 5
")->fetchAll();

// Recent inquiries
$inquiries = $pdo->query("
    SELECT i.*, p.title AS property_title
    FROM inquiries i
    LEFT JOIN properties p ON i.property_id = p.id
    ORDER BY i.created_at DESC
    LIMIT 5
")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-stats">
    <div class="admin-stat">
        <div class="admin-stat-icon blue"><i class="fas fa-building"></i></div>
        <div>
            <div class="admin-stat-num"><?= $total_properties ?></div>
            <div class="admin-stat-label">Total Properties</div>
        </div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="admin-stat-num"><?= $total_available ?></div>
            <div class="admin-stat-label">Available</div>
        </div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-icon gold"><i class="fas fa-handshake"></i></div>
        <div>
            <div class="admin-stat-num"><?= $total_sold ?></div>
            <div class="admin-stat-label">Sold / Leased</div>
        </div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-icon red"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="admin-stat-num"><?= $new_inquiries ?></div>
            <div class="admin-stat-label">New Inquiries</div>
        </div>
    </div>
</div>

<div class="admin-card">
    <h2>Quick Actions</h2>
    <div style="display:flex; gap:.75rem; flex-wrap:wrap;">
        <a href="<?= url('admin/property-form.php') ?>" class="btn-admin-primary">
            <i class="fas fa-plus"></i> Add New Property
        </a>
        <a href="<?= url('admin/properties.php') ?>" class="btn-admin-accent">
            <i class="fas fa-list"></i> Manage Properties
        </a>
        <a href="<?= url('admin/inquiries.php') ?>" class="btn-admin-ghost">
            <i class="fas fa-envelope"></i> View Inquiries
        </a>
        <a href="<?= url('admin/locations.php') ?>" class="btn-admin-ghost">
            <i class="fas fa-map-marker-alt"></i> Manage Locations
        </a>
    </div>
</div>

<div class="admin-card">
    <h2>Recent Properties</h2>
    <?php if (count($recent) > 0): ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent as $p): ?>
                        <tr>
                            <td><img class="thumb" src="<?= e(property_image_url($p['primary_image'] ?? null)) ?>" alt=""></td>
                            <td>
                                <strong><?= e($p['title']) ?></strong><br>
                                <small style="color:#888;"><?= e($p['location_name'] ?? '—') ?></small>
                            </td>
                            <td><?= e(listing_type_label($p['listing_type'])) ?> · <?= e(property_type_label($p['property_type'])) ?></td>
                            <td><?= e(format_price((float)$p['price'], $p['price_period'])) ?></td>
                            <td><span class="status-pill status-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
                            <td>
                                <div class="row-actions">
                                    <a href="<?= url('admin/property-form.php?id=' . $p['id']) ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty">
            <i class="fas fa-building"></i>
            <h3>No properties yet</h3>
            <p>Add your first property to get started.</p>
        </div>
    <?php endif; ?>
</div>

<div class="admin-card">
    <h2>Recent Inquiries</h2>
    <?php if (count($inquiries) > 0): ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Property</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $i): ?>
                        <tr>
                            <td>
                                <strong><?= e($i['name']) ?></strong><br>
                                <small style="color:#888;"><?= e($i['email']) ?></small>
                            </td>
                            <td><?= e($i['property_title'] ?? '— General —') ?></td>
                            <td><span class="status-pill status-<?= e($i['status']) ?>"><?= e($i['status']) ?></span></td>
                            <td><?= date('M j, Y', strtotime($i['created_at'])) ?></td>
                            <td>
                                <div class="row-actions">
                                    <a href="<?= url('admin/inquiries.php#inq-' . $i['id']) ?>" class="btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty">
            <i class="fas fa-envelope-open"></i>
            <h3>No inquiries yet</h3>
            <p>Messages from customers will show up here.</p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>