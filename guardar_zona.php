<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para marcar zonas.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['lat']) || !isset($data['lng'])) {
    echo json_encode(['success' => false, 'message' => 'Coordenadas no proporcionadas.']);
    exit();
}

$lat = $data['lat'];
$lng = $data['lng'];
$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("INSERT INTO zonas_usuarios (lat, lng, usuario_id) VALUES (?, ?, ?)");
$stmt->bind_param("ddi", $lat, $lng, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la zona: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
