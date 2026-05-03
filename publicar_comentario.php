<?php
require_once 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sitio = $_POST['sitio'];
    $valoracion = $_POST['valoracion'];
    $comentario = $_POST['comentario'];
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    $usuario_nombre = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Invitado';
    
    // Manejo de la foto
    $foto_ruta = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $nombre_archivo = time() . "_" . basename($_FILES['foto']['name']);
        $foto_ruta = "uploads/" . $nombre_archivo;
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto_ruta);
    }

    $stmt = $conn->prepare("INSERT INTO comentarios (sitio, valoracion, comentario, foto, usuario_id, usuario_nombre) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $sitio, $valoracion, $comentario, $foto_ruta, $usuario_id, $usuario_nombre);

    if ($stmt->execute()) {
        header("Location: perfil.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
