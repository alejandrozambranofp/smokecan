<?php
require 'conexion.php';
$res = $conn->query("DESCRIBE usuario");
$cols = [];
while ($row = $res->fetch_assoc()) {
    $cols[] = $row['Field'];
}
echo implode(', ', $cols);
?>
