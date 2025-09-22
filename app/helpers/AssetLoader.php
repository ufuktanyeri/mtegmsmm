<?php
/**
 * Asset Loader Helper
 * Automatically loads minified or regular assets based on environment
 *
 * @version 1.0.0
 * @date 2025-01-21
 */

class AssetLoader {

    /**
     * Get CSS file path based on environment
     *
     * @param string $filename Base filename without extension (e.g., 'app', 'admin')
     * @param bool $bundle Use bundled version if available
     * @return string Full URL to CSS file
     */
    public static function css($filename = 'app', $bundle = false) {
        $isProduction = defined('APP_ENV') && APP_ENV === 'production';
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/';

        // In production, try to use bundled version first
        if ($isProduction && $bundle) {
            $bundlePath = 'assets/app.min.css';
            $bundleFile = PUBLIC_PATH . $bundlePath;

            if (file_exists($bundleFile)) {
                return $baseUrl . $bundlePath . '?v=' . filemtime($bundleFile);
            }
        }

        // Check for minified version
        if ($isProduction) {
            $minPath = "assets/css/{$filename}.min.css";
            $minFile = PUBLIC_PATH . $minPath;

            if (file_exists($minFile)) {
                return $baseUrl . $minPath . '?v=' . filemtime($minFile);
            }
        }

        // Fall back to regular version
        $regularPath = "assets/css/{$filename}.css";
        $regularFile = PUBLIC_PATH . $regularPath;

        if (file_exists($regularFile)) {
            return $baseUrl . $regularPath . '?v=' . filemtime($regularFile);
        }

        // Return path anyway (might be external)
        return $baseUrl . $regularPath;
    }

    /**
     * Get JavaScript file path based on environment
     *
     * @param string $filename Base filename without extension
     * @param bool $bundle Use bundled version if available
     * @return string Full URL to JS file
     */
    public static function js($filename = 'app', $bundle = false) {
        $isProduction = defined('APP_ENV') && APP_ENV === 'production';
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/';

        // In production, try to use bundled version first
        if ($isProduction && $bundle) {
            $bundlePath = 'assets/app.min.js';
            $bundleFile = PUBLIC_PATH . $bundlePath;

            if (file_exists($bundleFile)) {
                return $baseUrl . $bundlePath . '?v=' . filemtime($bundleFile);
            }
        }

        // Check for minified version
        if ($isProduction) {
            $minPath = "assets/js/{$filename}.min.js";
            $minFile = PUBLIC_PATH . $minPath;

            if (file_exists($minFile)) {
                return $baseUrl . $minPath . '?v=' . filemtime($minFile);
            }
        }

        // Fall back to regular version
        $regularPath = "assets/js/{$filename}.js";
        $regularFile = PUBLIC_PATH . $regularPath;

        if (file_exists($regularFile)) {
            return $baseUrl . $regularPath . '?v=' . filemtime($regularFile);
        }

        // Return path anyway (might be external)
        return $baseUrl . $regularPath;
    }

    /**
     * Get image path with cache busting
     *
     * @param string $filename Image filename with extension
     * @param string $folder Subfolder in img directory
     * @return string Full URL to image
     */
    public static function img($filename, $folder = '') {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/';

        $path = 'assets/img/';
        if (!empty($folder)) {
            $path .= $folder . '/';
        }
        $path .= $filename;

        $fullPath = PUBLIC_PATH . $path;

        if (file_exists($fullPath)) {
            return $baseUrl . $path . '?v=' . filemtime($fullPath);
        }

        return $baseUrl . $path;
    }

    /**
     * Generate complete CSS link tags for views
     *
     * @param array|string $files CSS files to load
     * @param bool $bundle Use bundled version
     * @return string HTML link tags
     */
    public static function cssLinks($files, $bundle = false) {
        if (!is_array($files)) {
            $files = [$files];
        }

        $html = '';

        // If bundling is requested and available in production, load only bundle
        if ($bundle && defined('APP_ENV') && APP_ENV === 'production') {
            $bundleUrl = self::css('app', true);
            if (strpos($bundleUrl, 'app.min.css') !== false) {
                return '<link rel="stylesheet" href="' . htmlspecialchars($bundleUrl) . '">' . "\n";
            }
        }

        // Load individual files
        foreach ($files as $file) {
            $url = self::css($file);
            $html .= '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">' . "\n";
        }

        return $html;
    }

    /**
     * Generate complete JS script tags for views
     *
     * @param array|string $files JS files to load
     * @param bool $bundle Use bundled version
     * @param bool $defer Add defer attribute
     * @return string HTML script tags
     */
    public static function jsScripts($files, $bundle = false, $defer = true) {
        if (!is_array($files)) {
            $files = [$files];
        }

        $html = '';
        $deferAttr = $defer ? ' defer' : '';

        // If bundling is requested and available in production, load only bundle
        if ($bundle && defined('APP_ENV') && APP_ENV === 'production') {
            $bundleUrl = self::js('app', true);
            if (strpos($bundleUrl, 'app.min.js') !== false) {
                return '<script src="' . htmlspecialchars($bundleUrl) . '"' . $deferAttr . '></script>' . "\n";
            }
        }

        // Load individual files
        foreach ($files as $file) {
            $url = self::js($file);
            $html .= '<script src="' . htmlspecialchars($url) . '"' . $deferAttr . '></script>' . "\n";
        }

        return $html;
    }

    /**
     * Preload critical assets for performance
     *
     * @param array $assets Array of assets to preload
     * @return string HTML preload tags
     */
    public static function preload($assets = []) {
        $html = '';

        foreach ($assets as $asset) {
            $type = $asset['type'] ?? 'style';
            $file = $asset['file'] ?? '';

            if (empty($file)) continue;

            switch ($type) {
                case 'style':
                case 'css':
                    $url = self::css($file);
                    $html .= '<link rel="preload" href="' . htmlspecialchars($url) . '" as="style">' . "\n";
                    break;

                case 'script':
                case 'js':
                    $url = self::js($file);
                    $html .= '<link rel="preload" href="' . htmlspecialchars($url) . '" as="script">' . "\n";
                    break;

                case 'font':
                    $url = BASE_URL . 'assets/fonts/' . $file;
                    $html .= '<link rel="preload" href="' . htmlspecialchars($url) . '" as="font" type="font/woff2" crossorigin>' . "\n";
                    break;

                case 'image':
                    $folder = $asset['folder'] ?? '';
                    $url = self::img($file, $folder);
                    $html .= '<link rel="preload" href="' . htmlspecialchars($url) . '" as="image">' . "\n";
                    break;
            }
        }

        return $html;
    }

    /**
     * Get asset version for cache busting
     *
     * @param string $file File path relative to public directory
     * @return string Version string
     */
    public static function version($file) {
        $fullPath = PUBLIC_PATH . $file;

        if (file_exists($fullPath)) {
            return filemtime($fullPath);
        }

        return '1.0.0';
    }
}