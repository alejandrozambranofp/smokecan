<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

$logeado = isset($_SESSION['usuario_id']);
$rol = $logeado && isset($_SESSION['rol']) ? $_SESSION['rol'] : null;

echo json_encode([
    'logeado' => $logeado,
    'es_admin' => $rol === 'admin'
]);
?>
