<?php
require 'conexion.php';

// 1. Limpiar todo
$conn->query("TRUNCATE TABLE zonas_oficiales");

// 2. Solo el CAP (El que funciona perfecto)
$sql = "INSERT INTO zonas_oficiales (nombre, lat, lng, radio, tipo, nivel) VALUES 
    ('Centre d\'Atenció Primària Molins de Rei', 41.4185, 2.0127, 80, 'hospital', 'prohibido')";

if ($conn->query($sql) === TRUE) {
    echo "<h1>Solo el CAP restaurado</h1>";
    echo "<p>He eliminado las otras 3 zonas que no encajaban bien.</p>";
    echo "<p><a href='index.html'>Volver al Mapa</a></p>";
} else {
    echo "Error al insertar: " . $conn->error;
}

$conn->close();
?>
