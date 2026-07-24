<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$description = isset($data['description']) ? sanitizeInput($data['description']) : '';
$severity = isset($data['severity']) && in_array($data['severity'], ['low', 'medium', 'high']) 
    ? $data['severity'] 
    : 'low';

try {
    $stmt = $pdo->prepare("INSERT INTO anomalies (patient_id, description, severity) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $description, $severity]);
    
    echo json_encode(['success' => true, 'anomaly_id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>