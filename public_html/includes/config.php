<?php
declare(strict_types=1);

/**
 * Vision Exim — single source of truth for base URL/path.
 *
 * This auto-detects the public base path from DOCUMENT_ROOT so the site works
 * both when installed at domain root ("/") and in a subfolder (e.g. "/vision_exim").
 */

/* =========================================================================
 * 1. DATABASE CONFIGURATION
 * ========================================================================= */
if (!defined('VE_DB_HOST')) define('VE_DB_HOST', getenv('VE_DB_HOST') ?: (getenv('DB_HOST') ?: 'localhost'));
if (!defined('VE_DB_PORT')) define('VE_DB_PORT', (int)(getenv('VE_DB_PORT') ?: (getenv('DB_PORT') ?: 3307)));
if (!defined('VE_DB_NAME')) define('VE_DB_NAME', getenv('VE_DB_NAME') ?: (getenv('DB_NAME') ?: 'a1676fyx_tushar'));
if (!defined('VE_DB_USER')) define('VE_DB_USER', getenv('VE_DB_USER') ?: (getenv('DB_USER') ?: 'a1676fyx_tushar'));
if (!defined('VE_DB_PASS')) define('VE_DB_PASS', getenv('VE_DB_PASS') !== false ? getenv('VE_DB_PASS') : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'TusharTheGreatFuckBoy0704'));


/* =========================================================================
 * 2. URL HELPERS
 * ========================================================================= */
if (!function_exists('ve_base_url')) {
    function ve_base_url(): string
    {
        static $base = null;
        if ($base !== null) {
            return $base;
        }

        $projectRoot = realpath(__DIR__ . '/..'); // .../vision_exim
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath((string)$_SERVER['DOCUMENT_ROOT']) : false;

        if ($projectRoot && $docRoot) {
            $projectRootNorm = str_replace('\\', '/', $projectRoot);
            $docRootNorm = rtrim(str_replace('\\', '/', $docRoot), '/');

            if ($docRootNorm !== '' && str_starts_with($projectRootNorm, $docRootNorm)) {
                $rel = substr($projectRootNorm, strlen($docRootNorm));
                $rel = '/' . trim((string)$rel, '/');
                $base = ($rel === '/') ? '' : $rel;
                return $base;
            }
        }

        // Fallback: best-effort guess from SCRIPT_NAME (works for /admin/* too)
        $script = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
        $dir = rtrim(dirname($script), '/');
        if ($dir === '.' || $dir === '') {
            $base = '';
            return $base;
        }

        // If current script is under /admin, strip it to get the public base.
        $dir = preg_replace('#/admin$#', '', $dir);
        $base = ($dir === '/') ? '' : $dir;
        return $base;
    }
}

if (!function_exists('ve_url')) {
    /**
     * Build a site URL under the detected base URL.
     *
     * Examples:
     * - ve_url('index.php') => /vision_exim/index.php OR /index.php
     * - ve_url('')         => /vision_exim       OR /
     */
    function ve_url(string $path = ''): string
    {
        $base = ve_base_url();
        $path = ltrim($path, '/');

        if ($path === '') {
            return ($base === '') ? '/' : $base;
        }

        return ($base === '') ? ('/' . $path) : ($base . '/' . $path);
    }
}

