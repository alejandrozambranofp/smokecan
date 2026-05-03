<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
    exit();
}

$uid = $_SESSION['usuario_id'];
$accion = $_POST['accion'] ?? '';
$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID no válido.']);
    exit();
}

// Verificar que el comentario pertenece al usuario
$check = $conn->query("SELECT id FROM comentarios WHERE id = $id AND usuario_id = $uid");
if ($check->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para gestionar este comentario.']);
    exit();
}

switch ($accion) {
    case 'borrar':
        $sql = "DELETE FROM comentarios WHERE id = $id";
        break;

    case 'editar':
        $nuevo_texto = $conn->real_escape_string($_POST['comentario'] ?? '');
        if (empty($nuevo_texto)) {
            echo json_encode(['success' => false, 'message' => 'El comentario no puede estar vacío.']);
            exit();
        }
        // Al editar, vuelve a estar pendiente
        $sql = "UPDATE comentarios SET comentario = '$nuevo_texto', estado = 'pendiente' WHERE id = $id";
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        exit();
}

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $conn->error]);
}

$conn->close();
?>
