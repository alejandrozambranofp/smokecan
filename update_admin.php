<?php
require 'conexion.php';

// 1. Añadir columna rol si no existe
$conn->query("ALTER TABLE usuario ADD COLUMN rol ENUM('user', 'admin') DEFAULT 'user' AFTER password");

// 2. Hacer administrador al usuario que tú quieras (puedes cambiar el email)
// En este caso, buscaremos el primero que exista o uno específico
$email_admin = "admin@smokecan.com"; // Cambia esto si tienes un usuario específico
$sql = "UPDATE usuario SET rol = 'admin' WHERE email = '$email_admin' OR id = 1 LIMIT 1";

if ($conn->query($sql) === TRUE) {
    echo "Base de datos actualizada. Se ha asignado un administrador.";
} else {
    echo "Error actualizando base de datos: " . $conn->error;
}

$conn->close();
?>
