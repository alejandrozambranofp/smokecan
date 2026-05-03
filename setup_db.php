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
    echo "Tabla 'comentarios' creada o ya existe.\n";
} else {
    echo "Error creando tabla: " . $conn->error . "\n";
}

$sql_usuario = "CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_usuario) === TRUE) {
    echo "Tabla 'usuario' creada o ya existe.\n";
} else {
    echo "Error creando tabla usuario: " . $conn->error . "\n";
}

$conn->close();
?>
