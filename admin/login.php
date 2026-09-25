<?php
require_once __DIR__ . '/../includes/init.php';

// If already logged in, go to dashboard
if (!empty($_SESSION['admin_id'])) {
    redirect(url('admin/index.php'));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_name'] = $user['fullname'];
            $_SESSION['admin_role'] = $user['role'];
            flash_success('Welcome back, ' . $user['fullname'] . '!');
            redirect(url('admin/index.php'));
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — <?= e(site_name()) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= url('assets/images/logo.svg') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('admin/assets/admin.css') ?>">
</head>
<body>
<div class="admin-login-page">
    <div class="admin-login-card">
        <img src="<?= url('assets/images/logo.svg') ?>" width="56" height="56" alt="" class="logo">
        <h1>Admin Login</h1>
        <p class="sub">Sign in to manage <?= e(site_name()) ?></p>

        <?php if ($error): ?>
            <div class="form-errors">
                <p><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></p>
            </div>
        <?php endif; ?>

        <?php
        $flashes = flash_get();
        foreach ($flashes as $f):
        ?>
            <div class="flash flash-<?= e($f['type']) ?>" style="margin-bottom:1rem;">
                <i class="fas fa-info-circle"></i> <?= e($f['message']) ?>
            </div>
        <?php endforeach; ?>

        <form method="post" autocomplete="off">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required autofocus
                       value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="admin-login-footer">
            <a href="<?= url('/') ?>">← Back to site</a>
        </div>
    </div>
</div>
</body>
</html>