<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

$logeado = isset($_SESSION['usuario_id']);
$rol = $logeado && isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
$avatar = null;

if ($logeado) {
    $check_col = $conn->query("SHOW COLUMNS FROM usuario LIKE 'avatar'");
    if ($check_col->num_rows == 0) {
        $conn->query("ALTER TABLE usuario ADD COLUMN avatar VARCHAR(255) DEFAULT 'img/icono-usuario.png'");
    }
    $uid = $_SESSION['usuario_id'];
    $user_data = $conn->query("SELECT avatar FROM usuario WHERE id = $uid")->fetch_assoc();
    $avatar = $user_data['avatar'] ?: 'img/icono-usuario.png';
}

echo json_encode([
    'logeado' => $logeado,
    'es_admin' => $rol === 'admin',
    'avatar' => $avatar
]);
?>
