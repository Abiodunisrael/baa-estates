<?php
/** Public properties list with filters + pagination. */

$page_title = 'Properties';
$page_description = 'Browse all available houses and lands for sale or lease.';

// --- Read filters ---
$listing  = $_GET['listing']  ?? '';
$type     = $_GET['type']     ?? '';
$location = $_GET['location'] ?? '';
$min      = $_GET['min']      ?? '';
$max      = $_GET['max']      ?? '';
$q        = trim($_GET['q']   ?? '');
$sort     = $_GET['sort']     ?? 'newest';
$page     = max(1, (int)($_GET['page'] ?? 1));
$offset   = ($page - 1) * PER_PAGE;

// --- Build query ---
$where  = ["p.status = 'available'"];
$params = [];

if (in_array($listing, ['sale', 'lease'], true)) {
    $where[] = 'p.listing_type = ?';
    $params[] = $listing;
}
if (in_array($type, ['house', 'land'], true)) {
    $where[] = 'p.property_type = ?';
    $params[] = $type;
}
if ($location !== '' && ctype_digit((string)$location)) {
    $where[] = 'p.location_id = ?';
    $params[] = (int)$location;
}
if ($min !== '' && is_numeric($min)) {
    $where[] = 'p.price >= ?';
    $params[] = (float)$min;
}
if ($max !== '' && is_numeric($max)) {
    $where[] = 'p.price <= ?';
    $params[] = (float)$max;
}
if ($q !== '') {
    $where[] = '(p.title LIKE ? OR p.description LIKE ? OR p.address LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like);
}

$where_sql = 'WHERE ' . implode(' AND ', $where);

$order_map = [
    'newest'     => 'p.created_at DESC',
    'oldest'     => 'p.created_at ASC',
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
];
$order_sql = $order_map[$sort] ?? $order_map['newest'];

// --- Count for pagination ---
$count_sql = "SELECT COUNT(*) FROM properties p $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = (int)$count_stmt->fetchColumn();
$total_pages = max(1, (int)ceil($total / PER_PAGE));

// --- Fetch page ---
$sql = "
    SELECT p.*, l.name AS location_name,
           (SELECT filename FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS primary_image
    FROM properties p
    LEFT JOIN locations l ON p.location_id = l.id
    $where_sql
    ORDER BY $order_sql
    LIMIT " . (int)PER_PAGE . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();

$locations = $pdo->query("SELECT id, name FROM locations ORDER BY name")->fetchAll();

// Helper to build a query string with current filters
if (!function_exists('filter_url')) {
    function filter_url(array $overrides = []): string {
        $q = array_merge($_GET, $overrides);
        $q = array_filter($q, fn($v) => $v !== '' && $v !== null);
        return url('properties') . (empty($q) ? '' : '?' . http_build_query($q));
    }
}

require __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>All Properties</h1>
        <p><?= $total ?> propert<?= $total === 1 ? 'y' : 'ies' ?> found</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="listing-layout">

            <!-- FILTERS -->
            <aside class="filters" id="filters">
                <button class="filters-toggle" type="button" aria-expanded="false">
                    <i class="fas fa-filter"></i> Filters
                    <i class="fas fa-chevron-down"></i>
                </button>

                <form method="get" action="<?= url('properties') ?>" class="filters-form" id="filters-form">
                    <div class="filter-group">
                        <label>Search</label>
                        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Title, address…">
                    </div>

                    <div class="filter-group">
                        <label>Listing</label>
                        <select name="listing" data-auto-submit>
                            <option value="">Any</option>
                            <option value="sale"  <?= $listing === 'sale'  ? 'selected' : '' ?>>For Sale</option>
                            <option value="lease" <?= $listing === 'lease' ? 'selected' : '' ?>>For Lease</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Type</label>
                        <select name="type" data-auto-submit>
                            <option value="">Any</option>
                            <option value="house" <?= $type === 'house' ? 'selected' : '' ?>>House</option>
                            <option value="land"  <?= $type === 'land'  ? 'selected' : '' ?>>Land</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Location</label>
                        <select name="location" data-auto-submit>
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= (int)$loc['id'] ?>" <?= (string)$location === (string)$loc['id'] ? 'selected' : '' ?>>
                                    <?= e($loc['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Min Price (₦)</label>
                        <input type="number" name="min" value="<?= e($min) ?>" placeholder="0" min="0">
                    </div>

                    <div class="filter-group">
                        <label>Max Price (₦)</label>
                        <input type="number" name="max" value="<?= e($max) ?>" placeholder="No limit" min="0">
                    </div>

                    <div class="filter-group">
                        <label>Sort by</label>
                        <select name="sort" data-auto-submit>
                            <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Newest first</option>
                            <option value="oldest"     <?= $sort === 'oldest'     ? 'selected' : '' ?>>Oldest first</option>
                            <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Price: low to high</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: high to low</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="<?= url('properties') ?>" class="btn btn-ghost btn-block">
                        <i class="fas fa-times"></i> Clear All
                    </a>
                </form>
            </aside>

            <!-- RESULTS -->
            <div class="results">
                <?php if (count($properties) > 0): ?>
                    <div class="property-grid">
                        <?php foreach ($properties as $p): ?>
                            <?php require __DIR__ . '/../includes/property-card.php'; ?>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <nav class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="<?= e(filter_url(['page' => $page - 1])) ?>" class="page-link">
                                    <i class="fas fa-chevron-left"></i> Prev
                                </a>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end   = min($total_pages, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <a href="<?= e(filter_url(['page' => $i])) ?>"
                                   class="page-link <?= $i === $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="<?= e(filter_url(['page' => $page + 1])) ?>" class="page-link">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state-block">
                        <i class="fas fa-search"></i>
                        <h3>No properties match your filters</h3>
                        <p>Try adjusting your search or <a href="<?= url('properties') ?>">clear all filters</a>.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>