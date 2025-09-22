<?php

/**
 * Prodüksiyon Güvenli Index
 * Eski ve yeni yapıyla uyumlu entry point
 */


// Güvenli config'i yükle - önce production_safe_config'i dene
$productionConfig = __DIR__ . '/production_safe_config.php';
$standardConfig = dirname(__DIR__) . '/app/config/config.php';
$oldConfig = dirname(__DIR__) . '/config/config.php';

if (file_exists($productionConfig)) {
    require_once $productionConfig;
} elseif (file_exists($standardConfig)) {
    require_once $standardConfig;
} elseif (file_exists($oldConfig)) {
    require_once $oldConfig;
} else {
    die('Configuration file not found. Please check your setup.');
}

// Custom error handler with Sentry integration
function customErrorHandler($errno, $errstr, $errfile, $errline)
{
    $log_file = dirname(__DIR__) . '/logs/error.log';
    $error_message = date('[Y-m-d H:i:s]') . " Error: [$errno] $errstr in $errfile on line $errline" . PHP_EOL;

    // Log klasörü yoksa oluştur (proje root'unda)
    if (!is_dir(dirname(__DIR__) . '/logs')) {
        mkdir(dirname(__DIR__) . '/logs', 0777, true);
    }

    // Local file logging (existing functionality)
    error_log($error_message, 3, $log_file);

    // Send to Sentry if available and not a suppressed error
    if (class_exists('\Sentry\SentrySdk') && $errno !== 0) {
        $exception = new ErrorException($errstr, 0, $errno, $errfile, $errline);
        \Sentry\captureException($exception);

        // Add context for multi-tenant system
        if (isset($_SESSION['user_id']) || isset($_SESSION['cove_id'])) {
            \Sentry\withScope(function (\Sentry\State\Scope $scope) {
                if (isset($_SESSION['user_id'])) {
                    $scope->setUser([
                        'id' => $_SESSION['user_id'],
                        'username' => $_SESSION['username'] ?? 'unknown'
                    ]);
                }
                if (isset($_SESSION['cove_id'])) {
                    $scope->setTag('cove_id', $_SESSION['cove_id']);
                    $scope->setTag('cove_name', $_SESSION['cove_name'] ?? 'unknown');
                }
                $scope->setTag('request_uri', $_SERVER['REQUEST_URI'] ?? '/');
            });
        }
    }

    // Development ortamında hatayı göster
    if ($_SERVER['SERVER_NAME'] == 'localhost') {
        echo "<pre style='background:#ffcccc; padding:10px;'>Error: $errstr in $errfile on line $errline</pre>";
    }
}

set_error_handler("customErrorHandler");

// Secure session configuration
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    ini_set('session.use_strict_mode', 1);
}
// Start session with security measures
if (session_status() == PHP_SESSION_NONE) {
    session_start();

    // Regenerate session ID periodically for security
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Config already loaded above

// Autoloader (required for Sentry)
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';

    // Initialize Sentry if DSN is configured
    $sentryDsn = Environment::get('SENTRY_DSN');
    if (!empty($sentryDsn)) {
        \Sentry\init([
            'dsn' => $sentryDsn,
            'environment' => Environment::get('SENTRY_ENVIRONMENT', 'development'),
            'release' => Environment::get('SENTRY_RELEASE', '1.0.0'),
            'traces_sample_rate' => Environment::get('APP_ENV') === 'production' ? 0.2 : 1.0,
            'profiles_sample_rate' => Environment::get('APP_ENV') === 'production' ? 0.2 : 1.0,
        ]);
    }
}

// Security headers
require_once dirname(__DIR__) . '/includes/Security.php';
Security::setSecurityHeaders();

// Basit autoloader: App\* namespace -> /app/* klasör yapısı
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $relative = substr($class, 4); // 'App\\' sonrası
        $path = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
});

// Varsa composer autoload (ileride eklenirse) yükle
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Router path - eski yapıyla uyumlu
$routerPath = dirname(__DIR__) . '/app/Router.php';
$newRouterPath = dirname(__DIR__) . '/app/router.php';

if (file_exists($routerPath)) {
    require_once $routerPath;
} elseif (file_exists($newRouterPath)) {
    require_once $newRouterPath;
} else {
    die('Router not found. Please check file structure.');
}

// Request handling (eski kodla aynı)
$router = new Router();
$requestUri = '';
$requestCustomParams = [];

if (isset($_GET)) {
    foreach ($_GET as $key => $value) {
        if ($key == 'url') {
            $requestUri = $value;
        } else {
            $requestCustomParams[$key] = $value;
        }
    }
}

try {
    $router->dispatch($requestUri, $requestCustomParams);
} catch (Exception $e) {
    // Error logging
    error_log('Router Error: ' . $e->getMessage());

    // User friendly error
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo '<h1>Error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
    } else {
        echo '<h1>Service Temporarily Unavailable</h1><p>Please try again later.</p>';
    }
}
