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
$nombre_sitio = $conn->real_escape_string($data['nombre_sitio'] ?? '');
$tipo_zona = $conn->real_escape_string($data['tipo_zona'] ?? 'libre_humo');
$usuario_id = $_SESSION['usuario_id'];

$stmt_limite = $conn->prepare("SELECT COUNT(*) AS total FROM zonas_usuarios WHERE usuario_id = ? AND DATE(fecha) = CURDATE()");
$stmt_limite->bind_param("i", $usuario_id);
$stmt_limite->execute();
$zonas_hoy = $stmt_limite->get_result()->fetch_assoc()['total'];
$stmt_limite->close();

// Si es zona para fumar, usa la otra tabla
if ($tipo_zona === 'para_fumar') {
    $stmt_limite2 = $conn->prepare("SELECT COUNT(*) AS total FROM zonas_para_fumar WHERE usuario_id = ? AND DATE(fecha) = CURDATE()");
    $stmt_limite2->bind_param("i", $usuario_id);
    $stmt_limite2->execute();
    $zonas_fumar_hoy = $stmt_limite2->get_result()->fetch_assoc()['total'];
    $stmt_limite2->close();
    
    if ($zonas_fumar_hoy >= 2) {
        echo json_encode([
            'success' => false,
            'message' => 'Has alcanzado el limite diario de 2 zonas para fumar. Podras marcar mas zonas manana.'
        ]);
        exit();
    }
} else {
    if ($zonas_hoy >= 2) {
        echo json_encode([
            'success' => false,
            'message' => 'Has alcanzado el limite diario de 2 zonas. Podras marcar mas zonas manana.'
        ]);
        exit();
    }
}

// Guardar en la tabla correspondiente
if ($tipo_zona === 'para_fumar') {
    $stmt = $conn->prepare("INSERT INTO zonas_para_fumar (lat, lng, usuario_id, nombre_sitio) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ddis", $lat, $lng, $usuario_id, $nombre_sitio);
} else {
    $stmt = $conn->prepare("INSERT INTO zonas_usuarios (lat, lng, usuario_id, nombre_sitio, tipo_zona) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ddiss", $lat, $lng, $usuario_id, $nombre_sitio, $tipo_zona);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la zona: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
