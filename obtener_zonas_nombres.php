<?php
require 'conexion.php';

header('Content-Type: application/json');

// Obtener todas las zonas con nombre_sitio
$sql = "SELECT DISTINCT z.id, z.nombre_sitio, z.lat, z.lng, u.nombre as autor
        FROM zonas_usuarios z
        JOIN usuario u ON z.usuario_id = u.id
        WHERE z.nombre_sitio IS NOT NULL AND z.nombre_sitio != ''
        ORDER BY z.nombre_sitio ASC";

$resultado = $conn->query($sql);
$zonas = [];

while ($fila = $resultado->fetch_assoc()) {
    $zonas[] = $fila;
}

echo json_encode($zonas);
$conn->close();
?>
