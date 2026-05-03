<?php
require 'conexion.php';

$zonas = [
    ["CAP Molins de Rei", 41.418514, 2.012755, 75, "hospital", "prohibido"],
    ["Parc de la Mariona", 41.405747, 2.021982, 110, "parque", "prohibido"],
    ["Escola El Palau", 41.411630, 2.016379, 80, "colegio", "prohibido"],
    ["Institut Bernat el Ferrer", 41.410682, 2.027206, 100, "colegio", "prohibido"],
    ["Escola l'Alzina", 41.414016, 2.022790, 70, "colegio", "prohibido"],
    ["Escola Castell Ciuró", 41.411030, 2.026207, 75, "colegio", "prohibido"],
    ["Escola Pont de la Cadena", 41.406292, 2.018489, 85, "colegio", "prohibido"],
    ["Parc de la Sèquia del Molí", 41.417575, 2.014021, 90, "parque", "prohibido"],
    ["Escola la Sínia", 41.41836, 2.01169, 80, "colegio", "prohibido"]
];

$conn->query("DELETE FROM zonas_oficiales"); // Limpiar antes de insertar

$stmt = $conn->prepare("INSERT INTO zonas_oficiales (nombre, lat, lng, radio, tipo, nivel) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($zonas as $zona) {
    $stmt->bind_param("sddiss", $zona[0], $zona[1], $zona[2], $zona[3], $zona[4], $zona[5]);
    $stmt->execute();
}

echo "Migración completada con éxito.";
$stmt->close();
$conn->close();
?>
