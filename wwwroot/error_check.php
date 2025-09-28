<?php
// UTF-8 charset başlığını ekle (en üstte olmalı)
   header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP Çalışıyor<br>";

// Config yüklenebiliyor mu?
$configPath = dirname(__DIR__) . '/app/config/config.php';
if(file_exists($configPath)) {
    echo "Config dosyası bulundu, yükleniyor...<br>";
    require_once $configPath;
    echo "Config yüklendi<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
    echo "DB_NAME: " . DB_NAME . "<br>";
} else {
    echo "❌ Config bulunamadı!<br>";
}
?>