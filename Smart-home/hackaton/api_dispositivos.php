<?php
header('Content-Type: application/json');
require 'conexion.php';

try {
    $sala_id = $_GET['sala_id'] ?? null;
    
    if (!$sala_id) {
        throw new Exception("Se requiere el parámetro sala_id");
    }

    // Obtener nombre de la sala
    $stmt = $conn->prepare("SELECT nombre FROM salas WHERE id = ?");
    $stmt->execute([$sala_id]);
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$sala) {
        throw new Exception("Sala no encontrada");
    }

    // Obtener dispositivos de la sala
    $stmt = $conn->prepare("
        SELECT id, nombre, marca, modelo, consumo_watts, ubicacion 
        FROM dispositivos 
        WHERE sala_id = ? AND estado = 'activo'
    ");
    $stmt->execute([$sala_id]);
    $dispositivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener consumo total histórico
    $stmt = $conn->prepare("
        SELECT SUM(c.consumo_kwh) as consumo_total
        FROM consumos c
        JOIN dispositivos d ON c.dispositivo_id = d.id
        WHERE d.sala_id = ?
    ");
    $stmt->execute([$sala_id]);
    $consumo_total = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'nombre_sala' => $sala['nombre'],
        'dispositivos' => $dispositivos,
        'consumo_total' => $consumo_total['consumo_total'] ?? 0
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>