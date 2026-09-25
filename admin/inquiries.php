<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page  = 'inquiries';
$admin_title = 'Customer Inquiries';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inq_id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if ($inq_id && in_array($status, ['new','read','replied'], true)) {
        $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?")->execute([$status, $inq_id]);
        flash_success('Inquiry status updated.');
    }
    redirect(url('admin/inquiries.php'));
}

// Optional filter
$filter = $_GET['status'] ?? '';
$where = '';
$params = [];
if (in_array($filter, ['new','read','replied'], true)) {
    $where = 'WHERE i.status = ?';
    $params[] = $filter;
}

$stmt = $pdo->prepare("
    SELECT i.*, p.title AS property_title, p.slug AS property_slug
    FROM inquiries i
    LEFT JOIN properties p ON i.property_id = p.id
    $where
    ORDER BY i.created_at DESC
");
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1rem;">
        <h2 style="margin:0; padding:0; border:0;">Inquiries (<?= count($inquiries) ?>)</h2>
        <form method="get" style="display:flex; gap:.5rem;">
            <select name="status" onchange="this.form.submit()" style="padding:.5rem .8rem; border:1px solid #d8d8d2; border-radius:6px;">
                <option value="">All</option>
                <option value="new"     <?= $filter === 'new'     ? 'selected' : '' ?>>New</option>
                <option value="read"    <?= $filter === 'read'    ? 'selected' : '' ?>>Read</option>
                <option value="replied" <?= $filter === 'replied' ? 'selected' : '' ?>>Replied</option>
            </select>
        </form>
    </div>

    <?php if (count($inquiries) > 0): ?>
        <?php foreach ($inquiries as $i): ?>
            <div class="admin-card" id="inq-<?= (int)$i['id'] ?>" style="padding:1rem 1.25rem; background:#fafaf7; margin-bottom:.75rem;">
                <div style="display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap; align-items:flex-start;">
                    <div>
                        <strong style="font-size:1rem;"><?= e($i['name']) ?></strong>
                        <span class="status-pill status-<?= e($i['status']) ?>" style="margin-left:.5rem;"><?= e($i['status']) ?></span>
                        <div style="font-size:.85rem; color:#666; margin-top:.25rem;">
                            <i class="fas fa-envelope"></i> <a href="mailto:<?= e($i['email']) ?>"><?= e($i['email']) ?></a>
                            <?php if ($i['phone']): ?>
                                &nbsp; <i class="fas fa-phone"></i> <a href="tel:<?= e($i['phone']) ?>"><?= e($i['phone']) ?></a>
                            <?php endif; ?>
                        </div>
                        <?php if ($i['property_title']): ?>
                            <div style="font-size:.85rem; margin-top:.35rem;">
                                <i class="fas fa-building"></i>
                                <a href="<?= url('property/' . e($i['property_slug'])) ?>" target="_blank">
                                    <?= e($i['property_title']) ?>
                                </a>
                            </div>
                        <?php else: ?>
                            <div style="font-size:.85rem; margin-top:.35rem; color:#888;">
                                <i class="fas fa-comment"></i> General inquiry
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="font-size:.8rem; color:#888; text-align:right;">
                        <?= date('M j, Y g:ia', strtotime($i['created_at'])) ?>
                    </div>
                </div>

                <div style="margin-top:.75rem; padding-top:.75rem; border-top:1px solid #e5e5e0; font-size:.9rem; color:#333; line-height:1.6;">
                    <?= nl2br(e($i['message'])) ?>
                </div>

                <div style="margin-top:.75rem; display:flex; gap:.5rem; flex-wrap:wrap;">
                    <?php foreach (['new','read','replied'] as $st): ?>
                        <?php if ($i['status'] !== $st): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
                                <input type="hidden" name="status" value="<?= $st ?>">
                                <button type="submit" class="btn-admin-ghost" style="padding:.4rem .8rem; font-size:.8rem;">
                                    Mark as <?= ucfirst($st) ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <a href="mailto:<?= e($i['email']) ?>?subject=Re: <?= $i['property_title'] ? urlencode($i['property_title']) : 'Your inquiry' ?>" class="btn-admin-primary" style="padding:.4rem .8rem; font-size:.8rem;">
                        <i class="fas fa-reply"></i> Reply via Email
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="admin-empty">
            <i class="fas fa-envelope-open"></i>
            <h3>No inquiries</h3>
            <p>Inquiries submitted on the site will appear here.</p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>