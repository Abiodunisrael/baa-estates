<?php
$page_title = 'Contact Us';
$page_description = 'Get in touch with ' . site_name() . '.';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '')                               $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
    if ($message === '')                            $errors[] = 'Please enter a message.';

    if (empty($errors)) {
        $ins = $pdo->prepare("INSERT INTO inquiries (property_id, name, email, phone, message) VALUES (NULL, ?, ?, ?, ?)");
        $ins->execute([$name, $email, $phone ?: null, $message]);
        flash_success('Message sent! We will get back to you shortly.');
        redirect(url('contact'));
    }
}

require __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you</p>
    </div>
</section>

<section class="section">
    <div class="container contact-layout">
        <div class="contact-info">
            <h2>Get in Touch</h2>
            <p>Have a question about a property? Want to schedule a viewing? Send us a message and our team will respond within 24 hours.</p>

            <div class="contact-blocks">
                <div class="contact-block">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>Office</h4>
                        <p><?= e(site_address()) ?></p>
                    </div>
                </div>
                <div class="contact-block">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4>Phone</h4>
                        <p><?= e(site_phone()) ?></p>
                    </div>
                </div>
                <div class="contact-block">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>Email</h4>
                        <p><?= e(site_email()) ?></p>
                    </div>
                </div>
                <div class="contact-block">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h4>Hours</h4>
                        <p>Mon – Sat: 9:00 AM – 6:00 PM</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form-wrap">
            <h2>Send a Message</h2>

            <?php if (!empty($errors)): ?>
                <div class="form-errors">
                    <?php foreach ($errors as $err): ?>
                        <p><i class="fas fa-exclamation-circle"></i> <?= e($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="contact-form">
                <label>
                    <span>Your Name *</span>
                    <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
                </label>
                <label>
                    <span>Email *</span>
                    <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
                </label>
                <label>
                    <span>Phone</span>
                    <input type="tel" name="phone" value="<?= e($_POST['phone'] ?? '') ?>">
                </label>
                <label>
                    <span>Message *</span>
                    <textarea name="message" rows="6" required><?= e($_POST['message'] ?? '') ?></textarea>
                </label>
                <button type="submit" class="btn btn-accent btn-block">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>