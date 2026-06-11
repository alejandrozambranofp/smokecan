<?php
require 'conexion.php';

header('Content-Type: application/json');

$tipo = $_GET['tipo'] ?? 'libre_humo';

if ($tipo === 'para_fumar') {
    // Obtener zonas para fumar
    $sql = "SELECT DISTINCT z.id, z.nombre_sitio, z.lat, z.lng, u.nombre as autor
            FROM zonas_para_fumar z
            JOIN usuario u ON z.usuario_id = u.id
            WHERE z.nombre_sitio IS NOT NULL AND z.nombre_sitio != ''
            ORDER BY z.nombre_sitio ASC";
} else {
    // Obtener zonas libres de humo
    $sql = "SELECT DISTINCT z.id, z.nombre_sitio, z.lat, z.lng, u.nombre as autor
            FROM zonas_usuarios z
            JOIN usuario u ON z.usuario_id = u.id
            WHERE z.nombre_sitio IS NOT NULL AND z.nombre_sitio != '' AND (z.tipo_zona = 'libre_humo' OR z.tipo_zona IS NULL)
            ORDER BY z.nombre_sitio ASC";
}

$resultado = $conn->query($sql);
$zonas = [];

while ($fila = $resultado->fetch_assoc()) {
    $zonas[] = $fila;
}

echo json_encode($zonas);
$conn->close();
?>
