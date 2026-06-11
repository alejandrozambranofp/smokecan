<?php
require 'conexion.php';

// Agregar columna nombre_sitio a zonas_usuarios si no existe
$sql = "ALTER TABLE zonas_usuarios ADD COLUMN nombre_sitio VARCHAR(255) DEFAULT NULL";

try {
    $conn->query($sql);
    echo "✓ Columna 'nombre_sitio' agregada a la tabla 'zonas_usuarios'";
} catch (Exception $e) {
    // Si ya existe, no hacer nada
    if ($conn->errno == 1060) { // Error de columna duplicada
        echo "✓ La columna 'nombre_sitio' ya existe";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
