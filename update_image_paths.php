<?php
require_once 'app/config/config.php';

try {
    $conn = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "Starting image path updates...\n";
    echo str_repeat("-", 80) . "\n";

    // Mevcut görsel dosyalarını al
    $availableImages = [
        'gazismm_haber_1.png',
        'gazismm_haber_2.jpg',
        'gazismm_haber_3.jpg',
        'gazismm_haber_4.jpg'
    ];

    // Update specific news items with actual images
    $updates = [
        ['id' => 17, 'image' => 'gazismm_haber_1.png', 'title' => 'Uluslararası Başarı'],
        ['id' => 16, 'image' => 'gazismm_haber_2.jpg', 'title' => 'Öğretmen Eğitimleri'],
        ['id' => 15, 'image' => 'gazismm_haber_3.jpg', 'title' => 'İşbirliği Protokolleri'],
        ['id' => 14, 'image' => 'gazismm_haber_4.jpg', 'title' => 'Endüstri 4.0']
    ];

    foreach ($updates as $update) {
        $stmt = $conn->prepare("UPDATE news SET frontpage_image = ? WHERE id = ?");
        $result = $stmt->execute([$update['image'], $update['id']]);

        if ($result) {
            echo "✓ Updated ID {$update['id']} ({$update['title']}) -> {$update['image']}\n";
        } else {
            echo "✗ Failed to update ID {$update['id']}\n";
        }
    }

    // Fix the old format paths
    echo "\nFixing old format paths (uploads/news/)...\n";

    $stmt = $conn->prepare("UPDATE news SET frontpage_image = 'gazismm_haber_1.png' WHERE id = 12");
    $stmt->execute();
    echo "✓ Updated ID 12 to use gazismm_haber_1.png\n";

    // Clear placeholder entries for recent news
    $stmt = $conn->prepare("UPDATE news SET frontpage_image = NULL WHERE frontpage_image = 'placeholder.svg' AND id IN (18, 13, 11)");
    $stmt->execute();
    echo "✓ Cleared placeholder values for news without images\n";

    echo str_repeat("-", 80) . "\n";
    echo "Update completed successfully!\n";

    // Show updated records
    echo "\nUpdated records:\n";
    echo str_repeat("-", 80) . "\n";

    $stmt = $conn->query("SELECT id, title, frontpage_image FROM news WHERE id >= 11 ORDER BY id DESC");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['id']} - " . substr($row['title'], 0, 40) . "... -> " .
             ($row['frontpage_image'] ?: 'NULL') . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}