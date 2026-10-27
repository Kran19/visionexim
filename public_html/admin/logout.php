<?php
/**
 * Vision Exim — Admin Sign Out
 */
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../includes/config.php';

// Empty session array
$_SESSION = [];

// Invalidate session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"] ?? '/',
        $params["domain"] ?? '',
        $params["secure"] ?? false,
        $params["httponly"] ?? true
    );
}

// Invalidate remember me cookie if present
setcookie('remember_admin', '', time() - 3600, '/');

if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

header("Location: " . ve_url('admin/index.php'));
exit;

