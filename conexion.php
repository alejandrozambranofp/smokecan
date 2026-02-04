<?php
// conexion.php

$servidor = "localhost";
$usuario_db = "root";     // Usuario por defecto en local
$password_db = "";        // Contraseña por defecto (vacía en XAMPP)
$nombre_db = "smokecan";  // Tu base de datos real

// Intentar conectar
$conn = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Falló la conexión: " . $conn->connect_error);
}

// Establecer el juego de caracteres a utf8 para evitar problemas con tildes/eñes
$conn->set_charset("utf8");
?>