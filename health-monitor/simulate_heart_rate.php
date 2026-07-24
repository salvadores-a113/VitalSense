<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$rate = isset($data['rate']) ? intval($data['rate']) : 0;

try {
    $stmt = $pdo->prepare("INSERT INTO heart_rate (patient_id, rate) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $rate]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>