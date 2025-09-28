<?php
/**
 * MTEGM SMM Portal Configuration
 * PHP 5.5 ve PHP 8.2 uyumlu
 */

// PHP version kontrolü
$phpVersion = phpversion();
$isOldPHP = version_compare($phpVersion, '5.6.0', '<');

// Environment.php - sadece PHP 5.6+ için
if (!$isOldPHP && file_exists(dirname(dirname(dirname(__FILE__))) . '/includes/Environment.php')) {
    require_once dirname(dirname(dirname(__FILE__))) . '/includes/Environment.php';
    Environment::load();
}

// Production kontrolü
$isProduction = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'mtegmsmm.meb.gov.tr');

// Settings
if ($isProduction) {
    define('BASE_URL', 'https://mtegmsmm.meb.gov.tr/');
    define('APP_ENV', 'production');
    define('APP_DEBUG', false);
    
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'fg5085Y3XU1aG48Qw');
    define('DB_USER', 'fg508_5Y3XU1aGwa');
    define('DB_PASS', 'Jk6C73Pf');
} else {
    define('BASE_URL', 'http://localhost/mtegmsmm/');
    define('APP_ENV', 'development');
    define('APP_DEBUG', true);
    
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'fg5085Y3XU1aG48Qw');
    define('DB_USER', 'fg508_5Y3XU1aGwa');
    define('DB_PASS', 'Jk6C73Pf');
}

define('DB_CHARSET', 'utf8mb4');

// Paths - PHP 5.5 uyumlu (dirname yerine realpath)
$rootPath = realpath(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR;
define('ROOT_PATH', $rootPath);
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
define('INCLUDES_PATH', ROOT_PATH . 'includes' . DIRECTORY_SEPARATOR);
define('WWWROOT_PATH', ROOT_PATH . 'wwwroot' . DIRECTORY_SEPARATOR);
define('LOGS_PATH', ROOT_PATH . 'logs' . DIRECTORY_SEPARATOR);

// Session
if (!defined('SESSION_NAME')) {
    define('SESSION_NAME', 'MTEGM_SESSION');
}

if (!defined('SESSION_LIFETIME')) {
    define('SESSION_LIFETIME', 3600); // 1 hour
}

// Session is started in index.php with proper security settings
// Do not start session here

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Autoloader - basit versiyon
spl_autoload_register(function ($class) {
    $paths = array(
        APP_PATH . 'controllers/',
        APP_PATH . 'models/',
        INCLUDES_PATH
    );
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});