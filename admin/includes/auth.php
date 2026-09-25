<?php
/**
 * Admin authentication guard.
 * Include at the top of every admin page (except login.php).
 */

// Load core init (session, DB, helpers) — go up two levels
require_once __DIR__ . '/../../includes/init.php';

// Redirect to login if not authenticated
if (empty($_SESSION['admin_id'])) {
    flash_error('Please log in to continue.');
    redirect(url('admin/login.php'));
}