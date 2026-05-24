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

$stmt_limite = $conn->prepare("SELECT COUNT(*) AS total FROM zonas_usuarios WHERE usuario_id = ? AND DATE(fecha) = CURDATE()");
$stmt_limite->bind_param("i", $usuario_id);
$stmt_limite->execute();
$zonas_hoy = $stmt_limite->get_result()->fetch_assoc()['total'];
$stmt_limite->close();

if ($zonas_hoy >= 2) {
    echo json_encode([
        'success' => false,
        'message' => 'Has alcanzado el limite diario de 2 zonas. Podras marcar mas zonas manana.'
    ]);
    exit();
}

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
