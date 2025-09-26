<?php
/**
 * MTEGM SMM Portal - Main Entry Point
 * MVC Router Entry Point with Session Control
 *
 * @version 2.1
 * @date 2025-01-27
 */

// Define application start time for performance monitoring
define('APP_START_TIME', microtime(true));

// Set error reporting level
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Define base paths
define('ROOT_DIR', dirname(__DIR__));
define('APP_DIR', ROOT_DIR . '/app');
define('CONFIG_DIR', ROOT_DIR . '/_dev/config');
define('PUBLIC_DIR', __DIR__);

// Load configuration
$configFile = CONFIG_DIR . '/config.php';
if (!file_exists($configFile)) {
    die('Configuration file not found. Please check your installation.');
}
require_once $configFile;

// Initialize custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Skip suppressed errors
    if (error_reporting() === 0) {
        return false;
    }

    $logDir = ROOT_DIR . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    $errorMessage = sprintf(
        "[%s] Error %d: %s in %s on line %d\n",
        date('Y-m-d H:i:s'),
        $errno,
        $errstr,
        $errfile,
        $errline
    );

    error_log($errorMessage, 3, $logDir . '/error.log');

    // In debug mode, display errors
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo "<div style='background:#ffcccc; padding:10px; margin:10px; border:1px solid #ff0000;'>";
        echo "<strong>Error:</strong> " . htmlspecialchars($errstr) . "<br>";
        echo "<strong>File:</strong> " . htmlspecialchars($errfile) . " (Line: $errline)";
        echo "</div>";
    }

    return true;
}

set_error_handler('customErrorHandler');

// Configure session security
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', SESSION_LIFETIME ?? 3600);

// Set secure cookie flag in production
if (defined('APP_ENV') && APP_ENV === 'production') {
    ini_set('session.cookie_secure', 1);
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME ?? 'MTEGMSMM_SESSION');
    session_start();

    // Session security: regenerate ID periodically
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
        $_SESSION['last_activity'] = time();
    } else {
        // Check session timeout
        if (time() - $_SESSION['last_activity'] > (SESSION_LIFETIME ?? 3600)) {
            // Session expired
            session_destroy();
            session_start();
            $_SESSION['created'] = time();
            $_SESSION['last_activity'] = time();
        } else {
            $_SESSION['last_activity'] = time();

            // Regenerate session ID every 30 minutes
            if (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Define public pages that don't require authentication
$publicPages = [
    '',                    // Home/landing page
    'auth/login',         // Login page
    'auth/logout',        // Logout action
    'auth/register',      // Registration page (if enabled)
    'auth/forgot',        // Forgot password
    'auth/reset',         // Reset password
    'public/about',       // About page
    'public/contact',     // Contact page
    'api/health',         // Health check endpoint
    'test/server',        // Server test (development only)
];

// Get current request URL
$requestUrl = $_GET['url'] ?? '';
$requestUrl = trim($requestUrl, '/');

// Check if authentication is required
$requiresAuth = true;
foreach ($publicPages as $publicPage) {
    if ($requestUrl === $publicPage || strpos($requestUrl, $publicPage . '/') === 0) {
        $requiresAuth = false;
        break;
    }
}

// In development mode, allow test pages
if (defined('APP_DEBUG') && APP_DEBUG && strpos($requestUrl, 'test/') === 0) {
    $requiresAuth = false;
}

// Check authentication
if ($requiresAuth && !isset($_SESSION['user_id'])) {
    // Store requested URL for redirect after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

    // Redirect to login page
    header('Location: ' . (BASE_URL ?? '/') . 'index.php?url=auth/login');
    exit;
}

// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Additional headers for production
if (defined('APP_ENV') && APP_ENV === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://cdn.jsdelivr.net https://code.jquery.com https://www.google.com https://www.gstatic.com; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src \'self\' https://fonts.gstatic.com data:; img-src \'self\' data: https:; frame-src https://www.google.com;');
}

// Register autoloader for application classes
spl_autoload_register(function ($className) {
    // Handle App namespace
    if (strpos($className, 'App\\') === 0) {
        $classPath = str_replace('\\', '/', substr($className, 4));
        $file = APP_DIR . '/' . $classPath . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }

    // Handle legacy classes without namespace
    $legacyPaths = [
        APP_DIR . '/controllers/' . $className . '.php',
        APP_DIR . '/models/' . $className . '.php',
        APP_DIR . '/helpers/' . $className . '.php',
        APP_DIR . '/libraries/' . $className . '.php',
        APP_DIR . '/' . $className . '.php',
    ];

    foreach ($legacyPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }
});

// Load Composer autoloader if exists
$composerAutoload = ROOT_DIR . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// Load helper functions
$helperFiles = [
    APP_DIR . '/helpers/functions.php',
    APP_DIR . '/helpers/security_helpers.php',
    APP_DIR . '/helpers/database_helpers.php',
];

foreach ($helperFiles as $helperFile) {
    if (file_exists($helperFile)) {
        require_once $helperFile;
    }
}

// Check maintenance mode
if (defined('MAINTENANCE_MODE') && MAINTENANCE_MODE) {
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
    $allowedIps = MAINTENANCE_ALLOWED_IPS ?? ['127.0.0.1', '::1'];

    if (!in_array($clientIp, $allowedIps)) {
        http_response_code(503);
        header('Retry-After: 3600');
        echo '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakım Modu - MTEGM SMM Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-warning text-center">
            <h1>Sistem Bakımda</h1>
            <p>' . (MAINTENANCE_MESSAGE ?? 'Sistem şu anda bakım modundadır. Lütfen daha sonra tekrar deneyiniz.') . '</p>
        </div>
    </div>
</body>
</html>';
        exit;
    }
}

// Load and initialize router
$routerFile = APP_DIR . '/router.php';
if (!file_exists($routerFile)) {
    // Try alternative locations
    $alternativeRouters = [
        APP_DIR . '/Router.php',
        ROOT_DIR . '/router.php',
    ];

    foreach ($alternativeRouters as $altRouter) {
        if (file_exists($altRouter)) {
            $routerFile = $altRouter;
            break;
        }
    }

    if (!file_exists($routerFile)) {
        die('Router not found. Please check your application structure.');
    }
}

require_once $routerFile;

// Initialize router and dispatch request
try {
    $router = new Router();

    // Prepare request parameters
    $requestParams = [];
    foreach ($_GET as $key => $value) {
        if ($key !== 'url') {
            $requestParams[$key] = $value;
        }
    }

    // Dispatch the request
    $router->dispatch($requestUrl, $requestParams);

} catch (Exception $e) {
    // Log the error
    error_log('Router Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());

    // Display error page
    http_response_code(500);

    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hata - MTEGM SMM Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-danger">
            <h1>Bir Hata Oluştu</h1>
            <p><strong>Hata Mesajı:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
            <p><strong>Dosya:</strong> ' . htmlspecialchars($e->getFile()) . '</p>
            <p><strong>Satır:</strong> ' . $e->getLine() . '</p>
            <hr>
            <pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>
        </div>
    </div>
</body>
</html>';
    } else {
        echo '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hata - MTEGM SMM Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-danger text-center">
            <h1>Beklenmeyen Bir Hata Oluştu</h1>
            <p>Sistem yöneticisi bilgilendirildi. Lütfen daha sonra tekrar deneyiniz.</p>
            <p><a href="' . (BASE_URL ?? '/') . '" class="btn btn-primary">Ana Sayfaya Dön</a></p>
        </div>
    </div>
</body>
</html>';
    }
}

// Log execution time in development
if (defined('APP_DEBUG') && APP_DEBUG) {
    $executionTime = microtime(true) - APP_START_TIME;
    if ($executionTime > 1) {
        error_log('Slow request: ' . $requestUrl . ' took ' . round($executionTime, 3) . ' seconds');
    }
}