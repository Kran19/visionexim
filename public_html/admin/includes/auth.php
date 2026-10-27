<?php
/**
 * Vision Exim — Authentication Guard
 * Include this at the very top of admin pages to ensure the user is logged in.
 */

// Enterprise session bootstrap
require_once __DIR__ . '/session.php';

// Ensure config is loaded so ve_url is available
require_once __DIR__ . '/../../includes/config.php';

// Check if user is logged in
if (!ve_is_admin_logged_in()) {
    // Redirect to login page
    header('Location: ' . ve_url('admin/index.php'));
    exit;
}

