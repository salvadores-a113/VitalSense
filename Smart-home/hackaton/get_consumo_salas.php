<?php
require 'conexion.php';

try {
    // Consulta para obtener el consumo total por sala
    $query = "
        SELECT 
            s.id,
            s.nombre AS sala,
            COUNT(d.id) AS cantidad_dispositivos,
            SUM(d.consumo_watts) AS potencia_total,
            SUM(c.consumo_kwh) AS consumo_total_kwh,
            SUM(c.consumo_kwh * 0.15) AS costo_aproximado  /* Asumiendo $0.15 por kWh */
        FROM salas s
        LEFT JOIN dispositivos d ON s.id = d.sala_id
        LEFT JOIN consumos c ON d.id = c.dispositivo_id
        GROUP BY s.id
        ORDER BY consumo_total_kwh DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $resultados,
        'last_updated' => date('Y-m-d H:i:s')
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error en la consulta: ' . $e->getMessage()
    ]);
}
?>