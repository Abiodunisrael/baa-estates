<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page  = 'change-password';
$admin_title = 'Change Password';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current'] ?? '';
    $new     = $_POST['new']     ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($current === '')   $errors[] = 'Enter your current password.';
    if (strlen($new) < 8)  $errors[] = 'New password must be at least 8 characters.';
    if ($new !== $confirm) $errors[] = 'New passwords do not match.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['admin_id']]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current, $user['password_hash'])) {
            $errors[] = 'Current password is incorrect.';
        } else {
            $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")
                ->execute([password_hash($new, PASSWORD_BCRYPT), $user['id']]);

            flash_success('Password changed successfully.');
            redirect(url('admin/index.php'));
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="admin-card" style="max-width:520px;">
    <h2>Change Your Password</h2>

    <?php if (!empty($errors)): ?>
        <div class="form-errors" style="margin-bottom:1rem;">
            <?php foreach ($errors as $e): ?>
                <p><i class="fas fa-exclamation-circle"></i> <?= e($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="admin-form" autocomplete="off">
        <div class="form-row">
            <div>
                <label class="req">Current Password</label>
                <input type="password" name="current" required autofocus>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label class="req">New Password</label>
                <input type="password" name="new" required minlength="8">
                <div class="form-help">At least 8 characters. Use a mix of letters, numbers, and symbols.</div>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label class="req">Confirm New Password</label>
                <input type="password" name="confirm" required minlength="8">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-admin-primary">
                <i class="fas fa-key"></i> Update Password
            </button>
            <a href="<?= url('admin/index.php') ?>" class="btn-admin-ghost">Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>