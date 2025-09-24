<?php
require_once 'app/config/config.php';

try {
    $conn = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // ID 11'deki haberin content'ini al
    $stmt = $conn->prepare("SELECT id, title, content FROM news WHERE id = 11");
    $stmt->execute();
    $news = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($news) {
        echo "News ID: " . $news['id'] . "\n";
        echo "Title: " . $news['title'] . "\n";
        echo str_repeat("-", 80) . "\n";
        echo "Content:\n";
        echo $news['content'] . "\n";
        echo str_repeat("-", 80) . "\n";

        // smmnetwork.html referansları ara
        if (strpos($news['content'], 'smmnetwork.html') !== false) {
            echo "\n⚠️ BULUNDU: smmnetwork.html referansı içerikte mevcut!\n";

            // Tüm smmnetwork.html linklerini bul
            preg_match_all('/href=["\']([^"\']*smmnetwork[^"\']*)["\']/', $news['content'], $matches);
            if (!empty($matches[1])) {
                echo "\nBulunan linkler:\n";
                foreach ($matches[1] as $link) {
                    echo "- " . $link . "\n";
                }
            }
        }
    }

    // Tüm haberlerde smmnetwork.html araması yap
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TÜM HABERLERDE ARAMA:\n";
    echo str_repeat("=", 80) . "\n";

    $stmt = $conn->query("SELECT id, title, content FROM news WHERE content LIKE '%smmnetwork%'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "\nHaber ID: " . $row['id'] . " - " . $row['title'] . "\n";
        preg_match_all('/href=["\']([^"\']*smmnetwork[^"\']*)["\']/', $row['content'], $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $link) {
                echo "  → " . $link . "\n";
            }
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}