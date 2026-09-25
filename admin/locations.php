<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page  = 'locations';
$admin_title = 'Locations';

// Handle add/update/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $loc_id = (int)($_POST['id'] ?? 0);
        $name   = trim($_POST['name'] ?? '');
        $state  = trim($_POST['state'] ?? '');
        $desc   = trim($_POST['description'] ?? '');

        if ($name === '') {
            flash_error('Location name is required.');
        } else {
            $slug = slugify($name);
            if ($loc_id > 0) {
                $pdo->prepare("UPDATE locations SET name = ?, slug = ?, state = ?, description = ? WHERE id = ?")
                    ->execute([$name, $slug, $state ?: null, $desc ?: null, $loc_id]);
                flash_success('Location updated.');
            } else {
                $pdo->prepare("INSERT INTO locations (name, slug, state, description) VALUES (?, ?, ?, ?)")
                    ->execute([$name, $slug, $state ?: null, $desc ?: null]);
                flash_success('Location added.');
            }
        }
    } elseif ($action === 'delete') {
        $loc_id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM locations WHERE id = ?")->execute([$loc_id]);
        flash_success('Location deleted. Associated properties now have no location.');
    }
    redirect(url('admin/locations.php'));
}

// Editing?
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = null;
if ($edit_id) {
    $s = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
    $s->execute([$edit_id]);
    $editing = $s->fetch();
}

// List with property counts
$locations = $pdo->query("
    SELECT l.*, (SELECT COUNT(*) FROM properties p WHERE p.location_id = l.id) AS property_count
    FROM locations l
    ORDER BY l.name
")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<style>
    .locations-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    @media (min-width: 900px) {
        .locations-layout {
            grid-template-columns: 1fr 2fr;
        }
    }
</style>

<div class="locations-layout">

    <div class="admin-card">
        <h2><?= $editing ? 'Edit Location' : 'Add Location' ?></h2>
        <form method="post" class="admin-form">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editing ? (int)$editing['id'] : 0 ?>">

            <div class="form-row">
                <div>
                    <label class="req">Name</label>
                    <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div>
                    <label>State</label>
                    <input type="text" name="state" value="<?= e($editing['state'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div>
                    <label>Description</label>
                    <textarea name="description" rows="4"><?= e($editing['description'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-save"></i> <?= $editing ? 'Update' : 'Add' ?>
                </button>
                <?php if ($editing): ?>
                    <a href="<?= url('admin/locations.php') ?>" class="btn-admin-ghost">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <h2>All Locations (<?= count($locations) ?>)</h2>
        <?php if (count($locations) > 0): ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>State</th>
                            <th>Properties</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locations as $loc): ?>
                            <tr>
                                <td><strong><?= e($loc['name']) ?></strong></td>
                                <td><?= e($loc['state'] ?? '—') ?></td>
                                <td><?= (int)$loc['property_count'] ?></td>
                                <td>
                                    <div class="row-actions">
                                        <a href="<?= url('admin/locations.php?edit=' . $loc['id']) ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="post" style="display:inline;" data-confirm="Delete this location?">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int)$loc['id'] ?>">
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
                <i class="fas fa-map-marker-alt"></i>
                <h3>No locations yet</h3>
                <p>Add your first location to organize properties.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>