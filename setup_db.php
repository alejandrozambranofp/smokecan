<?php
require_once 'conexion.php';

$sql = "CREATE TABLE IF NOT EXISTS comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sitio VARCHAR(255) NOT NULL,
    valoracion INT NOT NULL,
    comentario TEXT,
    foto VARCHAR(255),
    usuario_nombre VARCHAR(100),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'comentarios' creada o ya existe.";
} else {
    echo "Error creando tabla: " . $conn->error;
}

$conn->close();
?>
