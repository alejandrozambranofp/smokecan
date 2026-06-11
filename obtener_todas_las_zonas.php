<?php
require 'conexion.php';

// Desactivar caché del navegador
header("Cache-Control: no-cache, must-revalidate"); 
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 
header('Content-Type: application/json');

// 1. Obtener zonas oficiales
$sql_oficiales = "SELECT *, 'oficial' as origen FROM zonas_oficiales";
$res_oficiales = $conn->query($sql_oficiales);
$todas_zonas = [];

while ($fila = $res_oficiales->fetch_assoc()) {
    $todas_zonas[] = $fila;
}

// 2. Obtener zonas de usuarios con votos
$sql_usuarios = "SELECT z.id, z.lat, z.lng, z.usuario_id, z.nombre_sitio, z.fecha, z.tipo_zona, u.nombre as autor, 'usuario' as origen,
        (SELECT COUNT(*) FROM votos_zonas v WHERE v.zona_id = z.id AND v.voto = 1) as votos_si,
        (SELECT COUNT(*) FROM votos_zonas v WHERE v.zona_id = z.id AND v.voto = 0) as votos_no
        FROM zonas_usuarios z
        JOIN usuario u ON z.usuario_id = u.id";
$res_usuarios = $conn->query($sql_usuarios);

while ($fila = $res_usuarios->fetch_assoc()) {
    $todas_zonas[] = $fila;
}

// 3. Obtener zonas para fumar
$sql_fumar = "SELECT z.id, z.lat, z.lng, z.usuario_id, z.nombre_sitio, z.fecha, 'para_fumar' as tipo_zona, u.nombre as autor, 'usuario_fumar' as origen,
        0 as votos_si, 0 as votos_no
        FROM zonas_para_fumar z
        JOIN usuario u ON z.usuario_id = u.id";
$res_fumar = $conn->query($sql_fumar);

if ($res_fumar) {
    while ($fila = $res_fumar->fetch_assoc()) {
        $todas_zonas[] = $fila;
    }
}

echo json_encode($todas_zonas);
$conn->close();
?>
