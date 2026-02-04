<?php
// ------------------------------------------------------------------
// LÓGICA PHP
// ------------------------------------------------------------------
session_start();
require 'conexion.php';

$mensaje = "";
$error = "";

// Si ya está logueado, no debería ver el registro
if (isset($_SESSION['usuario_id'])) {
    header("Location: bienvenida.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Limpiamos los datos de entrada
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellidos = $conn->real_escape_string($_POST['apellidos']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // 2. Comprobamos si el email ya existe
    $check_email = "SELECT id FROM usuario WHERE email = '$email'";
    $resultado = $conn->query($check_email);

    if ($resultado->num_rows > 0) {
        $error = "Este correo electrónico ya está registrado.";
    } else {
        // 3. Encriptamos la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // 4. Insertamos en la base de datos (usamos NOW() para la fecha actual)
        $sql = "INSERT INTO usuario (nombre, apellidos, email, password, fecha_registro) 
                VALUES ('$nombre', '$apellidos', '$email', '$password_hash', NOW())";

        if ($conn->query($sql) === TRUE) {
            // Éxito: Redirigimos al login con un parámetro para mostrar mensaje
            // Opcional: Podrías loguearlo automáticamente aquí si quisieras
            header("Location: login.php?registrado=1");
            exit();
        } else {
            $error = "Error en la base de datos: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SmokeCan</title>
    <link rel="stylesheet" href="css/estilo_registro.css">
</head>
<body>

    <div class="login-container">
        <h2>Crear una Cuenta</h2>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellidos" placeholder="Apellidos" required>
            
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required minlength="6">
            
            <button type="submit">Registrarse</button>
        </form>

        <div class="footer-links">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>

</body>
</html>