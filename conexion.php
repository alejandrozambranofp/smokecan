<?php
// conexion.php

// Detectar si estamos en el servidor de InfinityFree o en Local (XAMPP/Docker)
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'infinityfreeapp.com') !== false) {
    // CONFIGURACIÓN REMOTA (InfinityFree)
    $servidor = "sql200.infinityfree.com";
    $usuario_db = "if0_41821590";
    $password_db = "cachi20122004";
    $nombre_db = "if0_41821590_smokecan";
} else {
    // CONFIGURACIÓN LOCAL (XAMPP / Docker)
    $servidor = getenv('DB_HOST') ?: "localhost";
    $usuario_db = getenv('DB_USER') ?: "root";
    $password_db = getenv('DB_PASS') ?: "";
    $nombre_db = getenv('DB_NAME') ?: "smokecan";
}

// Intentar conectar
$conn = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Falló la conexión: " . $conn->connect_error);
}

// Establecer el juego de caracteres
$conn->set_charset("utf8");
?>