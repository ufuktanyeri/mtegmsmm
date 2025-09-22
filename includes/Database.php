<?php
// app/Helpers/Database.php

class Database
{
    private static ?Database $instance = null;
    private string $host;
    private string $user;
    private string $pass;
    private string $dbname;

    private ?PDO $dbh = null;
    private ?PDOStatement $stmt = null;
    private ?string $error = null;
    private ?QueryCache $cache = null;
    private bool $cacheEnabled = true;
    private ?string $lastSql = null;
    private ?array $lastParams = null;

    private function __construct()
    {
        // Use config constants instead of hardcoded values
        $this->host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $this->dbname = defined('DB_NAME') ? DB_NAME : '';
        $this->user = defined('DB_USER') ? DB_USER : '';
        $this->pass = defined('DB_PASS') ? DB_PASS : '';

        // Initialize query cache if available
        if (file_exists(dirname(__FILE__) . '/QueryCache.php')) {
            require_once dirname(__FILE__) . '/QueryCache.php';
            $this->cache = QueryCache::getInstance();
        }
        
        if (empty($this->dbname) || empty($this->user)) {
            throw new Exception("Database configuration is missing or incomplete");
        }
        
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        $options = array(
            PDO::ATTR_PERSISTENT => false, // Changed from true - prevents connection pool exhaustion
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_EMULATE_PREPARES => false, // Better performance for prepared statements
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true // Reduces memory for large result sets
        );

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Database Connection Error: " . $this->error);
            // Hata durumunda boş bir PDO döndürme, exception fırlat
            throw new Exception("Veritabanı bağlantısı kurulamadı: " . $this->error);
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance == null) {
            try {
                self::$instance = new Database();
            } catch (Exception $e) {
                die("Kritik Hata: Veritabanı bağlantısı kurulamadı.");
            }
        }
        return self::$instance;
    }

    public function query(string $sql, array $params = []): self
    {
        // Store for potential caching
        $this->lastSql = $sql;
        $this->lastParams = $params;

        if (!$this->dbh) {
            $error = "Database connection not established";
            error_log($error);
            
            // Send to Sentry if available
            if (class_exists('\Sentry\SentrySdk')) {
                \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($error, $sql) {
                    $scope->setTag('error_source', 'database');
                    $scope->setTag('operation', 'connection');
                    $scope->setLevel(\Sentry\Severity::fatal());
                    $scope->setContext('database', [
                        'sql' => $sql,
                        'host' => $this->host,
                        'database' => $this->dbname
                    ]);
                    \Sentry\captureMessage($error, \Sentry\Severity::fatal());
                });
            }
            
            throw new Exception($error);
        }

        try {
            $this->stmt = $this->dbh->prepare($sql);
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    if (is_string($key)) {
                        $this->bind($key, $value);
                    } else {
                        $this->bind($key + 1, $value);
                    }
                }
            }
        } catch (PDOException $e) {
            $error = "Database query preparation failed: " . $e->getMessage();
            error_log($error);
            
            // Send to Sentry if available
            if (class_exists('\Sentry\SentrySdk')) {
                \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($sql, $params, $e) {
                    $scope->setTag('error_source', 'database');
                    $scope->setTag('operation', 'prepare');
                    $scope->setLevel(\Sentry\Severity::error());
                    $scope->setContext('database', [
                        'sql' => $sql,
                        'params' => $params,
                        'pdo_error_code' => $e->getCode(),
                        'pdo_error_info' => $e->errorInfo ?? []
                    ]);
                    \Sentry\captureException($e);
                });
            }
            
            throw new Exception($error);
        }

        return $this;
    }

    public function bind(string|int $param, mixed $value, ?int $type = null): void
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    public function resultSet(): array
    {
        // Check cache for SELECT queries
        if ($this->cache && $this->cacheEnabled && $this->lastSql && stripos($this->lastSql, 'SELECT') === 0) {
            $cacheKey = $this->cache->generateKey($this->lastSql, $this->lastParams);
            $cachedResult = $this->cache->get($cacheKey);

            if ($cachedResult !== null) {
                return $cachedResult;
            }
        }

        $this->execute();
        $result = $this->stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cache the result if applicable
        if ($this->cache && $this->cacheEnabled && $this->lastSql && stripos($this->lastSql, 'SELECT') === 0) {
            $cacheKey = $this->cache->generateKey($this->lastSql, $this->lastParams);
            $this->cache->set($cacheKey, $result);
        }

        return $result;
    }

    public function single(): array|false
    {
        // Check cache for SELECT queries
        if ($this->cache && $this->cacheEnabled && $this->lastSql && stripos($this->lastSql, 'SELECT') === 0) {
            $cacheKey = $this->cache->generateKey($this->lastSql, $this->lastParams);
            $cachedResult = $this->cache->get($cacheKey);

            if ($cachedResult !== null) {
                return $cachedResult;
            }
        }

        $this->execute();
        $result = $this->stmt->fetch(PDO::FETCH_ASSOC);

        // Cache the result if applicable
        if ($this->cache && $this->cacheEnabled && $this->lastSql && stripos($this->lastSql, 'SELECT') === 0 && $result !== false) {
            $cacheKey = $this->cache->generateKey($this->lastSql, $this->lastParams);
            $this->cache->set($cacheKey, $result);
        }

        return $result;
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    public function lastInsertId(): string
    {
        return $this->dbh->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->dbh->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->dbh->commit();
    }

    public function rollback(): bool
    {
        return $this->dbh->rollBack();
    }

    /**
     * Enable or disable query caching
     */
    public function setCacheEnabled(bool $enabled): void
    {
        $this->cacheEnabled = $enabled;
    }

    /**
     * Clear query cache
     */
    public function clearCache(): bool
    {
        if ($this->cache) {
            return $this->cache->clear();
        }
        return false;
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        if ($this->cache) {
            return $this->cache->getStats();
        }
        return [];
    }
}
