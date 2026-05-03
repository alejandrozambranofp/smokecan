<?php
require 'conexion.php';

header('Content-Type: application/json');

// 1. Obtener zonas oficiales
$sql_oficiales = "SELECT *, 'oficial' as origen FROM zonas_oficiales";
$res_oficiales = $conn->query($sql_oficiales);
$todas_zonas = [];

while ($fila = $res_oficiales->fetch_assoc()) {
    $todas_zonas[] = $fila;
}

// 2. Obtener zonas de usuarios con votos
$sql_usuarios = "SELECT z.*, u.nombre as autor, 'usuario' as origen,
        (SELECT COUNT(*) FROM votos_zonas v WHERE v.zona_id = z.id AND v.voto = 1) as votos_si,
        (SELECT COUNT(*) FROM votos_zonas v WHERE v.zona_id = z.id AND v.voto = 0) as votos_no
        FROM zonas_usuarios z
        JOIN usuario u ON z.usuario_id = u.id";
$res_usuarios = $conn->query($sql_usuarios);

while ($fila = $res_usuarios->fetch_assoc()) {
    $todas_zonas[] = $fila;
}

echo json_encode($todas_zonas);
$conn->close();
?>
