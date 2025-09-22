<?php
/**
 * Query Cache Implementation
 * Provides caching layer for database queries to improve performance
 */

class QueryCache {
    private static ?QueryCache $instance = null;
    private string $cacheDir;
    private int $defaultTTL = 300; // 5 minutes default
    private array $memoryCache = []; // In-memory cache for current request

    private function __construct() {
        $this->cacheDir = dirname(__DIR__) . '/cache/queries/';

        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        // Clean old cache files on initialization
        $this->cleanExpiredCache();
    }

    public static function getInstance(): QueryCache {
        if (self::$instance === null) {
            self::$instance = new QueryCache();
        }
        return self::$instance;
    }

    /**
     * Get cached query result
     */
    public function get(string $key): mixed {
        // Check memory cache first
        if (isset($this->memoryCache[$key])) {
            return $this->memoryCache[$key]['data'];
        }

        $filename = $this->getCacheFilename($key);

        if (!file_exists($filename)) {
            return null;
        }

        $data = unserialize(file_get_contents($filename));

        // Check if cache is expired
        if ($data['expires'] < time()) {
            unlink($filename);
            return null;
        }

        // Store in memory cache for this request
        $this->memoryCache[$key] = $data;

        return $data['data'];
    }

    /**
     * Set cache for query result
     */
    public function set(string $key, mixed $data, int $ttl = null): bool {
        if ($ttl === null) {
            $ttl = $this->defaultTTL;
        }

        $cacheData = [
            'data' => $data,
            'expires' => time() + $ttl,
            'created' => time()
        ];

        // Store in memory cache
        $this->memoryCache[$key] = $cacheData;

        // Store in file cache
        $filename = $this->getCacheFilename($key);
        return file_put_contents($filename, serialize($cacheData)) !== false;
    }

    /**
     * Delete cache entry
     */
    public function delete(string $key): bool {
        unset($this->memoryCache[$key]);

        $filename = $this->getCacheFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }

        return true;
    }

    /**
     * Clear all cache
     */
    public function clear(): bool {
        $this->memoryCache = [];

        $files = glob($this->cacheDir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    /**
     * Generate cache key from SQL and parameters
     */
    public function generateKey(string $sql, array $params = []): string {
        $key = $sql;
        if (!empty($params)) {
            $key .= '::' . serialize($params);
        }
        return md5($key);
    }

    /**
     * Get cache filename
     */
    private function getCacheFilename(string $key): string {
        return $this->cacheDir . $key . '.cache';
    }

    /**
     * Clean expired cache files
     */
    private function cleanExpiredCache(): void {
        $files = glob($this->cacheDir . '*.cache');
        $now = time();

        foreach ($files as $file) {
            $data = @unserialize(file_get_contents($file));
            if ($data === false || $data['expires'] < $now) {
                @unlink($file);
            }
        }
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array {
        $files = glob($this->cacheDir . '*.cache');
        $totalSize = 0;
        $activeCount = 0;
        $now = time();

        foreach ($files as $file) {
            $totalSize += filesize($file);
            $data = @unserialize(file_get_contents($file));
            if ($data !== false && $data['expires'] > $now) {
                $activeCount++;
            }
        }

        return [
            'total_files' => count($files),
            'active_entries' => $activeCount,
            'total_size' => $totalSize,
            'memory_entries' => count($this->memoryCache)
        ];
    }
}