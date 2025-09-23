<?php
/**
 * Global Helper Functions
 *
 * This file contains global helper functions that are autoloaded by Composer
 * These functions are available throughout the application
 */

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
            return $matches[2];
        }

        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * Get configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config($key, $default = null)
    {
        static $config = null;

        if ($config === null) {
            $configFile = __DIR__ . '/../config/config.php';
            if (file_exists($configFile)) {
                $config = require $configFile;
            }
        }

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the base path of the application
     *
     * @param string $path
     * @return string
     */
    function base_path($path = '')
    {
        $basePath = realpath(__DIR__ . '/../..');
        return $path ? $basePath . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $basePath;
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the app path
     *
     * @param string $path
     * @return string
     */
    function app_path($path = '')
    {
        $appPath = base_path('app');
        return $path ? $appPath . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $appPath;
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the public/wwwroot path
     *
     * @param string $path
     * @return string
     */
    function public_path($path = '')
    {
        $publicPath = base_path('wwwroot');
        return $path ? $publicPath . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $publicPath;
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the storage path
     *
     * @param string $path
     * @return string
     */
    function storage_path($path = '')
    {
        $storagePath = base_path('storage');

        // Create storage directory if it doesn't exist
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        return $path ? $storagePath . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $storagePath;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset path
     *
     * @param string $path
     * @param bool $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        $path = ltrim($path, '/');

        // Check if we're in production
        $isProduction = isset($_SERVER['HTTP_HOST']) &&
                       strpos($_SERVER['HTTP_HOST'], 'meb.gov.tr') !== false;

        if ($isProduction || $secure === true) {
            $protocol = 'https://';
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        }

        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = $protocol . $host;

        // Add application subdirectory if needed
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        if ($baseDir !== '/' && $baseDir !== '\\') {
            $baseUrl .= $baseDir;
        }

        return $baseUrl . '/assets/' . $path;
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL for the application
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    function url($path = '', $params = [])
    {
        $baseUrl = '';

        // Determine base URL
        if (!empty($_SERVER['HTTP_HOST'])) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $baseUrl = $protocol . $_SERVER['HTTP_HOST'];

            // Add script directory if not in root
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $baseDir = dirname($scriptName);
            if ($baseDir !== '/' && $baseDir !== '\\') {
                $baseUrl .= $baseDir;
            }
        }

        // Build URL with path
        if ($path) {
            $baseUrl .= '/index.php?url=' . ltrim($path, '/');
        }

        // Add parameters
        if (!empty($params)) {
            $baseUrl .= '&' . http_build_query($params);
        }

        return $baseUrl;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a given URL
     *
     * @param string $url
     * @param int $status
     * @return void
     */
    function redirect($url, $status = 302)
    {
        header('Location: ' . $url, true, $status);
        exit;
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve an old input item
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old($key, $default = null)
    {
        return $_SESSION['old_input'][$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get the CSRF token value
     *
     * @return string
     */
    function csrf_token()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a CSRF token field
     *
     * @return string
     */
    function csrf_field()
    {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die (for debugging)
     *
     * @param mixed ...$vars
     * @return void
     */
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        die(1);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump variables (for debugging)
     *
     * @param mixed ...$vars
     * @return void
     */
    function dump(...$vars)
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
    }
}

if (!function_exists('logger')) {
    /**
     * Log a message to file
     *
     * @param string $message
     * @param string $level
     * @param array $context
     * @return void
     */
    function logger($message, $level = 'info', $context = [])
    {
        $logFile = storage_path('logs/' . date('Y-m-d') . '.log');
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message $contextStr" . PHP_EOL;

        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('abort')) {
    /**
     * Throw an HTTP exception
     *
     * @param int $code
     * @param string $message
     * @return void
     */
    function abort($code = 404, $message = '')
    {
        http_response_code($code);

        $errorMessages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        ];

        if (!$message) {
            $message = $errorMessages[$code] ?? 'Error';
        }

        // Check if we have a custom error view
        $errorView = app_path("views/errors/{$code}.php");
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo "<h1>Error {$code}</h1>";
            echo "<p>{$message}</p>";
        }

        exit;
    }
}

if (!function_exists('auth')) {
    /**
     * Get the authenticated user
     *
     * @return object|null
     */
    function auth()
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('session')) {
    /**
     * Get or set session values
     *
     * @param string|array|null $key
     * @param mixed $default
     * @return mixed
     */
    function session($key = null, $default = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($key === null) {
            return $_SESSION;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $_SESSION[$k] = $v;
            }
            return null;
        }

        return $_SESSION[$key] ?? $default;
    }
}

if (!function_exists('clean_input')) {
    /**
     * Clean user input
     *
     * @param string $data
     * @return string
     */
    function clean_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

if (!function_exists('is_ajax')) {
    /**
     * Check if the request is an AJAX request
     *
     * @return bool
     */
    function is_ajax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

if (!function_exists('json_response')) {
    /**
     * Send a JSON response
     *
     * @param mixed $data
     * @param int $status
     * @return void
     */
    function json_response($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('trans')) {
    /**
     * Translate a string (placeholder for future i18n)
     *
     * @param string $key
     * @param array $replace
     * @return string
     */
    function trans($key, $replace = [])
    {
        // For now, just return the key
        // In future, this can be extended to use translation files
        $translation = $key;

        // Replace placeholders
        foreach ($replace as $placeholder => $value) {
            $translation = str_replace(':' . $placeholder, $value, $translation);
        }

        return $translation;
    }
}