<?php
header('Content-Type: application/json');
require 'conexion.php';

try {
    // Obtener salas con consumo total
    $query = "
        SELECT 
            s.id, 
            s.nombre,
            IFNULL(SUM(c.consumo_kwh), 0) as consumo_total
        FROM salas s
        LEFT JOIN dispositivos d ON s.id = d.sala_id
        LEFT JOIN consumos c ON d.id = c.dispositivo_id
        GROUP BY s.id
        ORDER BY s.nombre
    ";
    
    $stmt = $conn->query($query);
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener histórico de consumo (últimos 7 días)
    foreach ($salas as &$sala) {
        $stmt = $conn->prepare("
            SELECT 
                DATE_FORMAT(c.fecha, '%Y-%m-%d') as dia,
                SUM(c.consumo_kwh) as consumo_diario
            FROM consumos c
            JOIN dispositivos d ON c.dispositivo_id = d.id
            WHERE d.sala_id = ?
            GROUP BY dia
            ORDER BY dia DESC
            LIMIT 7
        ");
        $stmt->execute([$sala['id']]);
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Rellenar array de histórico (7 días)
        $sala['historial'] = array_reverse(array_column($historial, 'consumo_diario'));
    }
    
    echo json_encode([
        'success' => true,
        'salas' => $salas
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
