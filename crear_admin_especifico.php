<?php
require 'conexion.php';

$nombre = "Administrador";
$apellidos = "Smokecan";
$email = "admin@gmail.com";
$password = password_hash("12345678", PASSWORD_DEFAULT);
$rol = "admin";

// Verificar si ya existe
$check = $conn->query("SELECT id FROM usuario WHERE email = '$email'");

if ($check->num_rows > 0) {
    // Si existe, lo actualizamos a admin
    $conn->query("UPDATE usuario SET rol = 'admin', password = '$password' WHERE email = '$email'");
    echo "Usuario admin actualizado correctamente.";
} else {
    // Si no existe, lo creamos
    $stmt = $conn->prepare("INSERT INTO usuario (nombre, apellidos, email, password, rol) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $apellidos, $email, $password, $rol);
    
    if ($stmt->execute()) {
        echo "Usuario admin creado correctamente.";
    } else {
        echo "Error al crear admin: " . $conn->error;
    }
}

$conn->close();
?>
