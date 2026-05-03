<?php
require 'conexion.php';

// Borrar todas las zonas oficiales
if ($conn->query("TRUNCATE TABLE zonas_oficiales")) {
    echo "<h1>¡MAPA VACIADO CON ÉXITO!</h1>";
    echo "<p>Si entras ahora al mapa, no debería aparecer ninguna zona oficial roja o amarilla.</p>";
    echo "<p><a href='index.html'>Ir al mapa para comprobar</a></p>";
} else {
    echo "Error al vaciar: " . $conn->error;
}

$conn->close();
?>
