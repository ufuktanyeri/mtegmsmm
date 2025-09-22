<?php
// AJAX endpoint for getting regulations by aim ID
// This file should only output JSON data, no HTML layout needed

header('Content-Type: application/json');

if (!isset($_GET['aimId']) || !is_numeric($_GET['aimId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid aim ID']);
    exit;
}

$aimId = (int) $_GET['aimId'];

try {
    // Here you would fetch regulations for the aim from the database
    // For now, return an empty array as placeholder
    $regulations = [];
    
    // Example structure:
    // $regulations = [
    //     ['regulationSourceNo' => 'REG-001', 'regulationDesc' => 'Sample regulation'],
    //     ['regulationSourceNo' => 'REG-002', 'regulationDesc' => 'Another regulation']
    // ];
    
    echo json_encode($regulations);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error occurred']);
}
?>