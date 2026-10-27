<?php
/**
 * Vision Exim — Enterprise-Grade Session Manager
 *
 * Resolves cPanel shared-hosting broken session_save_path issues
 * (e.g. /var/cpanel/php/sessions/ea-php83 failures), prevents header-already-sent errors,
 * secures session cookies against XSS/CSRF, and provides persistent login state.
 */

declare(strict_types=1);

// Prevent premature output from breaking HTTP redirects/headers
if (ob_get_level() === 0) {
    ob_start();
}

if (!function_exists('ve_init_session')) {
    function ve_init_session(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // 1. Resolve a guaranteed writable session save path
        $chosenPath = null;
        $currentPath = session_save_path();

        // Helper to test if a directory is genuinely writable
        $isWritableDir = static function (?string $dir): bool {
            if (empty($dir) || !@is_dir($dir)) {
                return false;
            }
            $testFile = rtrim($dir, '/\\') . '/.ve_sess_test_' . bin2hex(random_bytes(6));
            if (@file_put_contents($testFile, '1') !== false) {
                @unlink($testFile);
                return true;
            }
            return false;
        };

        // If the system default session_save_path is valid and writable
        if (!empty($currentPath)) {
            $checkPath = $currentPath;
            if (strpos($checkPath, ';') !== false) {
                $parts = explode(';', $checkPath);
                $checkPath = end($parts);
            }
            if ($isWritableDir($checkPath)) {
                $chosenPath = $checkPath;
            }
        }

        // Fallback candidate 1: cPanel account-level private tmp directory (outside public_html)
        if ($chosenPath === null && !empty($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = str_replace('\\', '/', realpath((string)$_SERVER['DOCUMENT_ROOT']) ?: (string)$_SERVER['DOCUMENT_ROOT']);
            $cpanelUserTmp = dirname($docRoot) . '/tmp';
            if ($isWritableDir($cpanelUserTmp)) {
                $cpanelSessDir = $cpanelUserTmp . '/sessions';
                if (!is_dir($cpanelSessDir)) {
                    @mkdir($cpanelSessDir, 0700, true);
                }
                if ($isWritableDir($cpanelSessDir)) {
                    $chosenPath = $cpanelSessDir;
                } else {
                    $chosenPath = $cpanelUserTmp;
                }
            }
        }

        // Fallback candidate 2: Project internal protected sessions directory
        if ($chosenPath === null) {
            $localSessionsDir = dirname(__DIR__) . '/sessions';
            if (!is_dir($localSessionsDir)) {
                @mkdir($localSessionsDir, 0700, true);
            }
            if ($isWritableDir($localSessionsDir)) {
                $chosenPath = $localSessionsDir;
            }
        }

        // Fallback candidate 3: System temp directory
        if ($chosenPath === null) {
            $sysTemp = sys_get_temp_dir();
            if ($isWritableDir($sysTemp)) {
                $chosenPath = $sysTemp;
            }
        }

        if ($chosenPath !== null) {
            session_save_path($chosenPath);
        }

        // 2. HTTPS detection (supports Cloudflare, AWS ALB, Nginx reverse proxy, etc.)
        $isHttps = (
            (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') ||
            (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
            (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') ||
            (isset($_SERVER['HTTP_CF_VISITOR']) && strpos((string)$_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false)
        );

        // 3. Secure session cookie configuration
        $cookieLifetime = 86400 * 14; // 14 days
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.gc_maxlifetime', (string)$cookieLifetime);

        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => $cookieLifetime,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } else {
            session_set_cookie_params(
                $cookieLifetime,
                '/; samesite=Lax',
                '',
                $isHttps,
                true
            );
        }

        // 4. Safely start the session
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
    }
}

// Automatically bootstrap session whenever this file is included
ve_init_session();

if (!function_exists('ve_is_admin_logged_in')) {
    function ve_is_admin_logged_in(): bool
    {
        return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
}

if (!function_exists('ve_regenerate_session')) {
    function ve_regenerate_session(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
    }
}
