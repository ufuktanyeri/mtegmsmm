<?php
require_once 'app/config/config.php';

try {
    $conn = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // ID 11 için smmbanner.png kullan
    $stmt = $conn->prepare("UPDATE news SET frontpage_image = 'smmbanner.png' WHERE id = 11");
    $stmt->execute();
    echo "✓ Updated ID 11 to use smmbanner.png\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}