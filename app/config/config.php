<?php

/**
 * MTEGM SMM Portal Configuration
 * Production-ready configuration with environment detection
 *
 * @version 2.1
 * @date 2025-01-24
 * @bootstrap 5.3.6
 */

// Load environment variables
require_once dirname(__DIR__, 2) . '/includes/Environment.php';
Environment::load();

// Detect production environment
$isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mtegmsmm.meb.gov.tr';

// ===========================
// ENVIRONMENT CONFIGURATION
// ===========================
if ($isProduction) {
    // PRODUCTION SETTINGS
    define('BASE_URL', 'https://mtegmsmm.meb.gov.tr/wwwroot/');
    define('APP_ENV', 'production');
    define('APP_DEBUG', false);

    // Production Database Configuration
    define('DB_HOST', Environment::get('DB_HOST', 'localhost'));
    define('DB_NAME', Environment::get('DB_NAME', 'fg5085Y3XU1aG48Qw'));
    define('DB_USER', Environment::get('DB_USER', 'fg508_5Y3XU1aGwa'));
    define('DB_PASS', Environment::get('DB_PASS', 'Jk6C73Pf'));
    define('DB_PORT', Environment::get('DB_PORT', '3306'));
    define('DB_CHARSET', 'utf8mb4');

    // Security settings for production
    define('SECURE_COOKIES', true);
    define('SESSION_LIFETIME', 3600); // 1 hour
    define('CSRF_TOKEN_LIFETIME', 1800); // 30 minutes

} else {
    // DEVELOPMENT SETTINGS
    define('BASE_URL', 'http://localhost/mtegmsmm/');
    define('APP_ENV', 'development');
    define('APP_DEBUG', true);

    // Development Database Configuration
    define('DB_HOST', Environment::get('DB_HOST', 'localhost'));
    define('DB_NAME', Environment::get('DB_NAME', 'fg5085Y3XU1aG48Qw'));
    define('DB_USER', Environment::get('DB_USER', 'fg508_5Y3XU1aGwa'));
    define('DB_PASS', Environment::get('DB_PASS', 'Jk6C73Pf'));
    define('DB_PORT', Environment::get('DB_PORT', '3306'));
    define('DB_CHARSET', 'utf8mb4');

    // Security settings for development
    define('SECURE_COOKIES', false);
    define('SESSION_LIFETIME', 7200); // 2 hours for dev
    define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour for dev
}

// ===========================
// COMMON SETTINGS
// ===========================

// Application Settings
define('APP_NAME', Environment::get('APP_NAME', 'MTEGM SMM Portal'));
define('APP_VERSION', Environment::get('APP_VERSION', '2.1.0'));
define('BOOTSTRAP_VERSION', '5.3.6');
define('JQUERY_VERSION', '3.7.1');
define('FONTAWESOME_VERSION', '6.4.0');
define('APP_TIMEZONE', Environment::get('APP_TIMEZONE', 'Europe/Istanbul'));

// Path Settings
define('ROOT_PATH', dirname(__DIR__, 2) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('PUBLIC_PATH', ROOT_PATH . 'wwwroot/');
define('UPLOAD_PATH', PUBLIC_PATH . 'uploads/');
define('LOG_PATH', ROOT_PATH . 'logs/');

// Session Settings
define('SESSION_NAME', 'MTEGMSMM_SESSID');
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', $isProduction ? '.meb.gov.tr' : 'localhost');

// Security Settings
define('PASSWORD_SALT', Environment::get('PASSWORD_SALT', 'SMM-2024-SECURE-SALT'));
define('ENCRYPTION_KEY', Environment::get('ENCRYPTION_KEY', 'default-encryption-key-change-in-production'));

// File Upload Settings
define('MAX_UPLOAD_SIZE', 10485760); // 10MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Email Settings (for future use)
define('SMTP_HOST', Environment::get('SMTP_HOST', ''));
define('SMTP_PORT', Environment::get('SMTP_PORT', '587'));
define('SMTP_USER', Environment::get('SMTP_USER', ''));
define('SMTP_PASS', Environment::get('SMTP_PASS', ''));
define('SMTP_FROM', Environment::get('SMTP_FROM', 'noreply@meb.gov.tr'));

// API Settings (for future integrations)
define('API_RATE_LIMIT', Environment::get('API_RATE_LIMIT', '100'));
define('API_TIME_WINDOW', Environment::get('API_TIME_WINDOW', '3600'));

// Logging Settings
define('LOG_LEVEL', $isProduction ? 'ERROR' : 'DEBUG');
define('LOG_ROTATION', true);
define('LOG_MAX_FILES', 30);

// Cache Settings (for future implementation)
define('CACHE_ENABLED', $isProduction);
define('CACHE_DRIVER', Environment::get('CACHE_DRIVER', 'cache'));
define('CACHE_PATH', ROOT_PATH . 'wwwroot/');
define('CACHE_TTL', 3600);

// ===========================
// SECURITY HEADERS
// ===========================
if (!defined('CLI_MODE')) {
    // Set timezone
    date_default_timezone_set(APP_TIMEZONE);

    // Security headers for production
    if ($isProduction) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// ===========================
// ERROR REPORTING
// ===========================
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . 'php_errors.log');
}

// ===========================
// VALIDATION
// ===========================
// Ensure critical constants are defined
$requiredConstants = ['BASE_URL', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($requiredConstants as $constant) {
    if (!defined($constant)) {
        die("Critical configuration missing: {$constant}");
    }
}

// Production validation - ensure we're using correct database
if ($isProduction && DB_NAME !== 'fg5085Y3XU1aG48Qw') {
    error_log("WARNING: Unexpected database name in production!");
    die("Production database configuration mismatch.");
}

// ===========================
// GOOGLE RECAPTCHA CONFIGURATION
// ===========================
// Production Keys for mtegmsmm.meb.gov.tr
define('RECAPTCHA_SITE_KEY', '6LdoHcErAAAAAKrvRRGqA-zrQkTBH_1TYa2wp7fx');
define('RECAPTCHA_SECRET_KEY', '6LdoHcErAAAAAALrW3isqLf7HysqAN2yPFP-0zvn');
define('RECAPTCHA_ENABLED', true);
