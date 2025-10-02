<?php
// filepath: app/Models/BaseModel.php

require_once __DIR__ . '/../../includes/Database.php'; // PDO wrapper yok; doğrudan PDO kullanacağız

class BaseModel {
    /** @var PDO */
    protected $pdo;

    public function __construct() {
        // Config sabitleri varsa onları kullan, yoksa fallback
        if (defined('DB_HOST')) {
            $host = DB_HOST; $db = DB_NAME; $user = DB_USER; $pass = DB_PASS; 
        } else {
            // Local fallback (gerekirse düzenlenir)
            $host = 'localhost'; $db='fg5085Y3XU1aG48Qw'; $user='fg508_5Y3XU1aGwa'; $pass='Jk6C73Pf';
        }
        $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Minimum hata çıktısı; log mekanizması varsa entegre edilebilir
            die('Veritabanı bağlantı hatası: ' . $e->getMessage());
        }
    }
}
?>
