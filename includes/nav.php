<?php
$current = $route ?? 'home';
?>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= url('/') ?>">
            <img src="<?= url('assets/images/logo.svg') ?>" alt="<?= e(site_name()) ?>" class="brand-logo">
            <span class="brand-text">
                <strong>BAA</strong> Estates Agency
            </span>
        </a>

        <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <nav class="site-nav" id="site-nav">
            <ul>
                <li><a href="<?= url('/') ?>" class="<?= $current === 'home' ? 'active' : '' ?>">Home</a></li>
                <li><a href="<?= url('properties?listing=sale') ?>" class="<?= $current === 'properties' ? 'active' : '' ?>">Buy</a></li>
                <li><a href="<?= url('properties?listing=lease') ?>">Rent / Lease</a></li>
                <li><a href="<?= url('properties?type=land') ?>">Land</a></li>
                <li><a href="<?= url('about') ?>" class="<?= $current === 'about' ? 'active' : '' ?>">About</a></li>
                <li><a href="<?= url('contact') ?>" class="<?= $current === 'contact' ? 'active' : '' ?>">Contact</a></li>
                <li class="nav-cta">
                    <a href="<?= url('admin/login.php') ?>" class="btn btn-outline btn-sm">
                        <i class="fas fa-user-lock"></i> Admin
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>