<?php
// Incluimos la conexión
include 'conexion.php';

echo "<h1>Prueba de conexión a 'smokecan'</h1>";

// Consulta simple para ver si la tabla responde
$sql = "SELECT count(*) as total FROM usuario";
$resultado = $conn->query($sql);

if ($resultado) {
    $fila = $resultado->fetch_assoc();
    echo "<p style='color:green; font-weight:bold;'>✅ CONEXIÓN EXITOSA</p>";
    echo "<p>Se ha encontrado la tabla <strong>'usuario'</strong>.</p>";
    echo "<p>Actualmente hay <strong>" . $fila['total'] . "</strong> usuarios registrados.</p>";
    
    // Verificamos si el campo password es seguro
    $check_col = $conn->query("SHOW COLUMNS FROM usuario LIKE 'password'");
    $col_info = $check_col->fetch_assoc();
    
    // Extraemos el número del tipo (ej: varchar(255) -> 255)
    preg_match('/\d+/', $col_info['Type'], $matches);
    $longitud = $matches[0] ?? 0;

    if ($longitud < 60) {
        echo "<p style='color:red;'>⚠️ ALERTA: Tu columna 'password' solo acepta $longitud caracteres. Necesitas al menos 60 para que el login funcione. Ejecuta el SQL que te di arriba.</p>";
    } else {
        echo "<p style='color:blue;'>✅ La columna password tiene longitud correcta ($longitud).</p>";
    }

} else {
    echo "<p style='color:red;'>❌ Error: Conecto a la base de datos, pero no encuentro la tabla 'usuario'.</p>";
    echo "Error SQL: " . $conn->error;
}
?>