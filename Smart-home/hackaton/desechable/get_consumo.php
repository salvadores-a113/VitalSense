<?php
include 'conexion.php';

$dispositivo_id = isset($_GET['id']) ? $_GET['id'] : 1;

$sql = "SELECT * FROM consumos WHERE dispositivo_id = $dispositivo_id ORDER BY fecha ASC";
$result = $conn->query($sql);

$consumos = array();
while ($row = $result->fetch_assoc()) {
    $consumos[] = $row;
}

echo json_encode($consumos);
?>
