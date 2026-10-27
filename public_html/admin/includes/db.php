<?php
/**
 * Vision Exim — Resilient Database Connection
 * All admin modules include this file to get a $conn (mysqli) handle.
 */

// Require the central config file which holds the database credentials
require_once __DIR__ . '/../../includes/config.php';

// Enable exception handling for mysqli (default in PHP 8.1+)
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

$conn = null;
$configuredPort = defined('VE_DB_PORT') ? (int)VE_DB_PORT : 3306;

try {
    // Attempt configured host and port
    $conn = new mysqli(VE_DB_HOST, VE_DB_USER, VE_DB_PASS, VE_DB_NAME, $configuredPort);
} catch (Throwable $e1) {
    // If connection failed and port is non-standard, attempt standard port 3306
    if ($configuredPort !== 3306) {
        try {
            $conn = new mysqli(VE_DB_HOST, VE_DB_USER, VE_DB_PASS, VE_DB_NAME, 3306);
        } catch (Throwable $e2) {
            // Attempt socket/default connection
            try {
                $conn = new mysqli(VE_DB_HOST, VE_DB_USER, VE_DB_PASS, VE_DB_NAME);
            } catch (Throwable $e3) {
                error_log("Database Connection Error: " . $e1->getMessage() . " | " . $e2->getMessage() . " | " . $e3->getMessage());
            }
        }
    } else {
        // Attempt socket/default connection
        try {
            $conn = new mysqli(VE_DB_HOST, VE_DB_USER, VE_DB_PASS, VE_DB_NAME);
        } catch (Throwable $e2) {
            error_log("Database Connection Error: " . $e1->getMessage() . " | " . $e2->getMessage());
        }
    }
}

if (!$conn || $conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;">
            <h2 style="color:#B9412E;">Database Connection Failed</h2>
            <p style="color:#666;">A database error occurred. Please verify your database configuration and try again.</p>
         </div>');
}

// Set charset
$conn->set_charset('utf8mb4');
