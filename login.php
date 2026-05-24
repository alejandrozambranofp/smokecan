<?php
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

    $stmt = $conn->prepare("SELECT id, nombre, apellidos, password, rol FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        if (password_verify($password_ingresada, $fila['password'])) {
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario_nombre'] = $fila['nombre'];
            $_SESSION['rol'] = $fila['rol'];
            $_SESSION['email'] = $email;

            setcookie("usuario_logeado", "1", time() + (86400 * 30), "/");

            header("Location: index.html");
            exit();
        } else {
            $error = "La contrasena es incorrecta.";
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
    <link rel="stylesheet" href="css/estilo_login.css?v=3">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesion</h2>
        <?php if (!empty($error)): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Correo electronico" required>
            <input type="password" name="password" placeholder="Contrasena" required>
            <button type="submit">Entrar</button>
        </form>
        <a href="index.html" class="btn-volver-inicio">Volver a inicio</a>
        <div class="footer-links">No tienes cuenta? <a href="registro.php">Registrate aqui</a></div>
    </div>
</body>
</html>
