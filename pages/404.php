<?php
$page_title = 'Page Not Found';
require __DIR__ . '/../includes/header.php';
?>
<section class="section text-center" style="padding: 6rem 1rem;">
    <h1 style="font-size: 5rem; color: var(--color-accent); line-height:1;">404</h1>
    <h2>Page Not Found</h2>
    <p style="color: var(--color-muted); max-width: 500px; margin: 1rem auto 2rem;">
        The page you're looking for doesn't exist or has been moved.
    </p>
    <a href="<?= url('/') ?>" class="btn btn-primary">
        <i class="fas fa-home"></i> Back to Home
    </a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>