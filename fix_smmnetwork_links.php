<?php
require_once 'app/config/config.php';

try {
    $conn = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "SMM Network linkleri düzeltiliyor...\n";
    echo str_repeat("-", 80) . "\n";

    // Önce mevcut durumu göster
    $stmt = $conn->prepare("SELECT id, title, content FROM news WHERE id = 11");
    $stmt->execute();
    $news = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Eski içerik:\n";
    echo $news['content'] . "\n\n";

    // Doğru link formatını belirle
    // Development için localhost, production için mtegmsmm.meb.gov.tr
    $isProduction = false; // localhost'tayız

    if ($isProduction) {
        $correctLink = 'https://mtegmsmm.meb.gov.tr/index.php?url=home/smmnetwork';
    } else {
        // Localhost için - BASE_URL kullanmadan direkt relative path
        $correctLink = '/mtegmsmm/index.php?url=home/smmnetwork';
    }

    // Yeni içerik oluştur
    $newContent = '<h3 class=""><a href="' . $correctLink . '"><b>SMM Haritası</b></a></h3>';

    // Güncelleme yap
    $updateStmt = $conn->prepare("UPDATE news SET content = ? WHERE id = 11");
    $updateStmt->execute([$newContent]);

    echo "✅ Güncelleme yapıldı!\n\n";
    echo "Yeni içerik:\n";
    echo $newContent . "\n\n";

    echo "Doğru URL formatları:\n";
    echo str_repeat("-", 40) . "\n";
    echo "Development (localhost):\n";
    echo "  /mtegmsmm/index.php?url=home/smmnetwork\n\n";
    echo "Production (mtegmsmm.meb.gov.tr):\n";
    echo "  https://mtegmsmm.meb.gov.tr/index.php?url=home/smmnetwork\n";

    // Tüm haberlerdeki smmnetwork.html referanslarını güncelle
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "Diğer haberlerde benzer linkler aranıyor...\n";

    // Tüm smmnetwork.html linklerini bul ve güncelle
    $searchStmt = $conn->query("SELECT id, title, content FROM news WHERE content LIKE '%smmnetwork.html%'");

    while ($row = $searchStmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['id'] == 11) continue; // ID 11 zaten güncellendi

        echo "\nHaber ID: " . $row['id'] . " - " . $row['title'] . "\n";

        // Eski linki yeni linkle değiştir
        $updatedContent = str_replace(
            'https://mtegmsmm.meb.gov.tr/smmnetwork.html',
            $correctLink,
            $row['content']
        );

        // Güncelle
        $updateOther = $conn->prepare("UPDATE news SET content = ? WHERE id = ?");
        $updateOther->execute([$updatedContent, $row['id']]);

        echo "  → Güncellendi\n";
    }

    echo "\n✅ Tüm smmnetwork linkleri düzeltildi!\n";

} catch (PDOException $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}