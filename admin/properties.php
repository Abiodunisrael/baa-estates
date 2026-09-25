<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page  = 'properties';
$admin_title = 'All Properties';

// Optional filters
$status_filter = $_GET['status'] ?? '';
$type_filter   = $_GET['type']   ?? '';

$where  = [];
$params = [];

if (in_array($status_filter, ['available','sold','leased','pending'], true)) {
    $where[] = 'p.status = ?';
    $params[] = $status_filter;
}
if (in_array($type_filter, ['house','land'], true)) {
    $where[] = 'p.property_type = ?';
    $params[] = $type_filter;
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
    SELECT p.*, l.name AS location_name,
           (SELECT filename FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS primary_image,
           (SELECT COUNT(*) FROM property_images pi WHERE pi.property_id = p.id) AS image_count
    FROM properties p
    LEFT JOIN locations l ON p.location_id = l.id
    $where_sql
    ORDER BY p.created_at DESC
");
$stmt->execute($params);
$properties = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1rem;">
        <h2 style="margin:0; padding:0; border:0;">Properties (<?= count($properties) ?>)</h2>
        <a href="<?= url('admin/property-form.php') ?>" class="btn-admin-primary">
            <i class="fas fa-plus"></i> Add Property
        </a>
    </div>

    <form method="get" style="display:flex; gap:.6rem; flex-wrap:wrap; margin-bottom:1rem;">
        <select name="status" onchange="this.form.submit()" style="padding:.5rem .8rem; border:1px solid #d8d8d2; border-radius:6px;">
            <option value="">All Status</option>
            <option value="available" <?= $status_filter === 'available' ? 'selected' : '' ?>>Available</option>
            <option value="pending"   <?= $status_filter === 'pending'   ? 'selected' : '' ?>>Pending</option>
            <option value="sold"      <?= $status_filter === 'sold'      ? 'selected' : '' ?>>Sold</option>
            <option value="leased"    <?= $status_filter === 'leased'    ? 'selected' : '' ?>>Leased</option>
        </select>
        <select name="type" onchange="this.form.submit()" style="padding:.5rem .8rem; border:1px solid #d8d8d2; border-radius:6px;">
            <option value="">All Types</option>
            <option value="house" <?= $type_filter === 'house' ? 'selected' : '' ?>>House</option>
            <option value="land"  <?= $type_filter === 'land'  ? 'selected' : '' ?>>Land</option>
        </select>
        <?php if ($status_filter || $type_filter): ?>
            <a href="<?= url('admin/properties.php') ?>" class="btn-admin-ghost">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (count($properties) > 0): ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $p): ?>
                        <tr>
                            <td><img class="thumb" src="<?= e(property_image_url($p['primary_image'] ?? null)) ?>" alt=""></td>
                            <td>
                                <strong><?= e($p['title']) ?></strong><br>
                                <small style="color:#888;"><?= e($p['address'] ?? '') ?></small>
                            </td>
                            <td><?= e($p['location_name'] ?? '—') ?></td>
                            <td>
                                <?= e(property_type_label($p['property_type'])) ?><br>
                                <small style="color:#888;"><?= e(listing_type_label($p['listing_type'])) ?></small>
                            </td>
                            <td><?= e(format_price((float)$p['price'], $p['price_period'])) ?></td>
                            <td><span class="status-pill status-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
                            <td><?= (int)$p['image_count'] ?></td>
                            <td>
                                <div class="row-actions">
                                    <a href="<?= url('property/' . e($p['slug'])) ?>" target="_blank" class="btn-view" title="View on site">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= url('admin/property-form.php?id=' . $p['id']) ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="post" action="<?= url('admin/property-delete.php') ?>" style="display:inline;" data-confirm="Delete &quot;<?= e($p['title']) ?>&quot;? This cannot be undone.">
                                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                        <button type="submit" class="btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
            <h3>No properties found</h3>
            <p>Try clearing filters or <a href="<?= url('admin/property-form.php') ?>">add a new property</a>.</p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>