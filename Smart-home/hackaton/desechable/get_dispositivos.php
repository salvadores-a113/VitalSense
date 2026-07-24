<?php
include 'conexion.php';

$sql = "SELECT * FROM dispositivos";
$result = $conn->query($sql);

$dispositivos = array();
while ($row = $result->fetch_assoc()) {
    $dispositivos[] = $row;
}

echo json_encode($dispositivos);
?>
