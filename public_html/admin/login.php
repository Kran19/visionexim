<?php
/**
 * Vision Exim — Login Route Alias
 * Canonical login interface is at admin/index.php
 */
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../includes/config.php';

header("Location: " . ve_url('admin/index.php'), true, 301);
exit;