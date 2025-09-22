<?php
// app/Helpers/Environment.php

class Environment
{
    private static $loaded = false;
    private static $data = [];

    /**
     * .env dosyasını yükle
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = dirname(__DIR__) . '/.env';
        }

        if (!file_exists($path)) {
            // .env dosyası yoksa sessizce devam et, default değerler kullanılacak
            self::$loaded = true;
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Yorum satırlarını atla
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // KEY=VALUE formatını parse et
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Tırnak işaretlerini kaldır
                $value = trim($value, '"\'');

                // Environment ve class data'ya ekle
                $_ENV[$key] = $value;
                putenv("$key=$value");
                self::$data[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Environment değişkeni al
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        // Önce $_ENV'den dene
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        // Sonra getenv ile dene
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        // En son internal data'dan dene
        if (isset(self::$data[$key])) {
            return self::$data[$key];
        }

        return $default;
    }

    /**
     * Environment değişkeni var mı kontrol et
     */
    public static function has($key)
    {
        return self::get($key) !== null;
    }

    /**
     * Boolean değeri al
     */
    public static function getBool($key, $default = false)
    {
        $value = self::get($key, $default);
        
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
    }

    /**
     * Integer değeri al
     */
    public static function getInt($key, $default = 0)
    {
        return (int) self::get($key, $default);
    }

    /**
     * Tüm environment değişkenlerini al (debug için)
     */
    public static function all()
    {
        if (!self::$loaded) {
            self::load();
        }
        return self::$data;
    }
}
?>