<?php
require 'conexion.php';

// Crear tabla zonas_para_fumar si no existe
$sql = "CREATE TABLE IF NOT EXISTS zonas_para_fumar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lat DECIMAL(10, 6) NOT NULL,
    lng DECIMAL(10, 6) NOT NULL,
    usuario_id INT NOT NULL,
    nombre_sitio VARCHAR(255),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
)";

$conn->query($sql);

// Agregar columna tipo_zona a zonas_usuarios si no existe
$sql_check = "SHOW COLUMNS FROM zonas_usuarios LIKE 'tipo_zona'";
$result = $conn->query($sql_check);

if ($result->num_rows == 0) {
    $sql_alter = "ALTER TABLE zonas_usuarios ADD COLUMN tipo_zona VARCHAR(50) DEFAULT 'libre_humo'";
    $conn->query($sql_alter);
}

echo "✓ Tablas y columnas creadas correctamente";

$conn->close();
?>
