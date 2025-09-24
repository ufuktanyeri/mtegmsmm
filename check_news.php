<?php
require_once 'app/config/config.php';
require_once 'includes/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();
$stmt = $conn->query('SELECT id, title, frontpage_image FROM news LIMIT 5');

echo "News table image paths:\n";
echo str_repeat("-", 80) . "\n";

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . "\n";
    echo "Title: " . substr($row['title'], 0, 50) . "\n";
    echo "Image: " . $row['frontpage_image'] . "\n";
    echo str_repeat("-", 40) . "\n";
}