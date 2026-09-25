<?php
/**
 * Bootstraps every request: sessions, errors, DB, helpers.
 * Include this at the top of every public and admin entry file.
 */

// Error handling
if (!defined('APP_ENV')) {
    require_once __DIR__ . '/../config/config.php';
}

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB
require_once __DIR__ . '/../config/database.php';

// Helpers
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/flash.php';