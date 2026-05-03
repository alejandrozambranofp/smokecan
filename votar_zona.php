<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para votar.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['zona_id']) || !isset($data['voto'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit();
}

$zona_id = $data['zona_id'];
$voto = $data['voto']; // 1 o 0
$usuario_id = $_SESSION['usuario_id'];

// INSERT ... ON DUPLICATE KEY UPDATE para permitir cambiar el voto
$stmt = $conn->prepare("INSERT INTO votos_zonas (zona_id, usuario_id, voto) VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE voto = ?");
$stmt->bind_param("iiii", $zona_id, $usuario_id, $voto, $voto);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar voto: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
