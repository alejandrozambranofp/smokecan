<?php
require 'conexion.php';

header('Content-Type: application/json');

$sql = "SELECT z.*, u.nombre as autor,
        (SELECT COUNT(*) FROM votos_zonas v WHERE v.zona_id = z.id AND v.voto = 1) as votos_si,
        (SELECT COUNT(*) FROM votos_zonas v WHERE v.zona_id = z.id AND v.voto = 0) as votos_no
        FROM zonas_usuarios z
        JOIN usuario u ON z.usuario_id = u.id";

$resultado = $conn->query($sql);
$zonas = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $zonas[] = $fila;
    }
}

echo json_encode($zonas);
$conn->close();
?>
