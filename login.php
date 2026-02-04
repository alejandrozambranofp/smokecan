<?php
// ------------------------------------------------------------------
// LÓGICA PHP
// ------------------------------------------------------------------
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

require 'conexion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $conn->real_escape_string($_POST['email']);
    $password_ingresada = $_POST['password'];

    $sql = "SELECT id, nombre, apellidos, password FROM usuario WHERE email = '$email'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        
        if (password_verify($password_ingresada, $fila['password'])) {
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['apellido'] = $fila['apellidos'];
            $_SESSION['email'] = $email;

            header("Location: index.html");
            exit();
        } else {
            $error = "La contraseña es incorrecta.";
        }
    } else {
        $error = "No existe cuenta con ese email.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmokeCan</title>
    
    <link rel="stylesheet" href="css/estilo_login.css">
    
</head>
<body>

    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($_GET['registrado'])): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
                ¡Cuenta creada con éxito! Ahora puedes entrar.
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        
        <div class="footer-links">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>

</body>
</html>