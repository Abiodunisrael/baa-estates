<?php
/**
 * BAA ESTATES AGENCY — Configuration Template
 *
 * Copy this file to config.php and fill in your environment values.
 * The real config.php is git-ignored and should never be committed.
 */

// ============================================================
// ENVIRONMENT SWITCH
// ============================================================
// true = live host, false = local development
define('IS_LIVE', false);


// ============================================================
// LOCAL SETTINGS (XAMPP / MAMP / etc.)
// ============================================================
if (!IS_LIVE) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'baa_estates');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    define('SITE_URL', 'http://localhost/baa_estates');
    define('APP_ENV',  'development');
}


// ============================================================
// LIVE SETTINGS
// ============================================================
else {
    define('DB_HOST', 'YOUR_DB_HOST');
    define('DB_NAME', 'YOUR_DB_NAME');
    define('DB_USER', 'YOUR_DB_USER');
    define('DB_PASS', 'YOUR_DB_PASSWORD');

    define('SITE_URL', 'https://yourdomain.com');
    define('APP_ENV',  'production');
}


// ============================================================
// SITE DEFAULTS (overridable from the `settings` table in admin)
// ============================================================
define('SITE_NAME_DEFAULT',    'BAA Estates Agency');
define('SITE_TAGLINE_DEFAULT', 'Find your perfect home or land');
define('SITE_EMAIL_DEFAULT',   'info@example.com');
define('SITE_PHONE_DEFAULT',   '+234 800 000 0000');
define('SITE_ADDRESS_DEFAULT', 'Your office address');


// ============================================================
// PATHS
// ============================================================
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_DIR', BASE_PATH . '/uploads/properties/');
define('UPLOAD_URL', SITE_URL . '/uploads/properties/');


// ============================================================
// UPLOADS
// ============================================================
define('MAX_UPLOAD_BYTES', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);


// ============================================================
// PAGINATION
// ============================================================
define('PER_PAGE', 9);