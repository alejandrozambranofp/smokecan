<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json');

// Seguridad: Solo admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

$accion = $_GET['accion'] ?? '';
$id = intval($_REQUEST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID no válido.']);
    exit();
}

switch ($accion) {
    case 'eliminar_usuario':
        // No permitir eliminarse a sí mismo
        if ($id == $_SESSION['usuario_id']) {
            echo json_encode(['success' => false, 'message' => 'No puedes eliminarte a ti mismo.']);
            exit();
        }
        
        // Primero eliminar votos del usuario (para evitar errores de FK si existen)
        $conn->query("DELETE FROM votos_zonas WHERE usuario_id = $id");
        // Eliminar comentarios del usuario
        $conn->query("DELETE FROM comentarios WHERE usuario_id = $id");
        // Eliminar zonas del usuario
        $conn->query("DELETE FROM zonas_usuarios WHERE usuario_id = $id");
        
        $sql = "DELETE FROM usuario WHERE id = $id";
        break;

    case 'cambiar_rol':
        $nuevo_rol = $conn->real_escape_string($_GET['rol'] ?? 'user');
        if (!in_array($nuevo_rol, ['user', 'admin'])) {
            echo json_encode(['success' => false, 'message' => 'Rol no válido.']);
            exit();
        }
        $sql = "UPDATE usuario SET rol = '$nuevo_rol' WHERE id = $id";
        break;

    case 'editar_usuario':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit();
        }
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $apellidos = $conn->real_escape_string($_POST['apellidos']);
        $email = $conn->real_escape_string($_POST['email']);
        $pass = $_POST['password'] ?? '';
        
        $update_pass = "";
        if (!empty($pass)) {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $update_pass = ", password = '$hashed'";
        }
        
        $sql = "UPDATE usuario SET nombre = '$nombre', apellidos = '$apellidos', email = '$email' $update_pass WHERE id = $id";
        break;

    case 'eliminar_comentario':
        $sql = "DELETE FROM comentarios WHERE id = $id";
        break;

    case 'aprobar_comentario':
        $sql = "UPDATE comentarios SET estado = 'aprobado' WHERE id = $id";
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']);
        exit();
}

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $conn->error]);
}

$conn->close();
?>
