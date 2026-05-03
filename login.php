<?php
session_start();

// Si ya tiene sesión, lo mandamos al mapa directamente
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

require 'conexion.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password_ingresada = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, nombre, apellidos, password FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        if (password_verify($password_ingresada, $fila['password'])) {
            // Guardamos datos en la sesión (Servidor)
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['apellido'] = $fila['apellidos'];
            $_SESSION['email'] = $email;

            // Creamos la cookie para que el JavaScript (Cliente) sepa que estamos dentro
            setcookie("usuario_logeado", "1", time() + (86400 * 30), "/");

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
    <title>Login - SmokeCan</title>
    <link rel="stylesheet" href="css/estilo_login.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if (!empty($error)): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        <div class="footer-links">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></div>
    </div>
</body>
</html>