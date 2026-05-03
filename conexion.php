<?php
// conexion.php

$servidor = getenv('DB_HOST') ?: "localhost";
$usuario_db = getenv('DB_USER') ?: "root";
$password_db = getenv('DB_PASS') ?: "";
$nombre_db = getenv('DB_NAME') ?: "smokecan";

// Intentar conectar
$conn = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Falló la conexión: " . $conn->connect_error);
}

// Establecer el juego de caracteres a utf8 para evitar problemas con tildes/eñes
$conn->set_charset("utf8");
?>