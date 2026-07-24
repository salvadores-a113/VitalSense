<?php
header('Content-Type: application/json');
require 'conexion.php';

$query = "SELECT salas.nombre, SUM(consumo.valor) AS total_consumo
          FROM dispositivos 
          JOIN salas ON dispositivos.sala_id = salas.id
          JOIN consumo ON consumo.dispositivo_id = dispositivos.id
          GROUP BY salas.nombre";

$result = $conn->query($query);
$reporte = [];

while ($row = $result->fetch_assoc()) {
    $reporte[] = $row;
}

echo json_encode($reporte);
?>
