<?php
session_start();
require 'conexion.php';

$mensaje = "";
$error = "";

if (isset($_SESSION['usuario_id'])) {
    header("Location: bienvenida.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt_check = $conn->prepare("SELECT id FROM usuario WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $resultado = $stmt_check->get_result();

    if ($resultado->num_rows > 0) {
        $error = "Este correo electronico ya esta registrado.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt_insert = $conn->prepare("INSERT INTO usuario (nombre, apellidos, email, password, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
        $stmt_insert->bind_param("ssss", $nombre, $apellidos, $email, $password_hash);

        if ($stmt_insert->execute()) {
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
    <link rel="stylesheet" href="css/style.css?v=8">
    <link rel="stylesheet" href="css/estilo_registro.css?v=4">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="cabecera-contenedor">
        <div class="cabecera-principal">
            <div class="cabecera-espacio-izq"></div>
            <div class="logo">
                <a href="index.html?v=8"><img src="img/smokecan-logo.svg" alt="Logo SMOKECAN"></a>
            </div>
            <button class="btn-hamburguesa" id="btn-menu-hamburguesa" aria-label="Menú">
                <span class="barra"></span>
                <span class="barra"></span>
                <span class="barra"></span>
            </button>
            <div class="cabecera-espacio-der"></div>
        </div>
        <nav class="cabecera-navegacion">
            <div class="enlaces-nav">
                <div class="enlace-item"><a href="index.html?v=8">Mapa</a></div>
                <div class="enlace-item"><a href="foro.php">Foro Smokecan</a></div>
                <div class="enlace-item"><a href="estancos.html">Estancos</a></div>
                <div class="enlace-item" id="nav-perfil-link"><a href="login.php">Mi Perfil</a></div>
            </div>
        </nav>
    </header>

    <main class="login-main">
        <div class="login-container">
            <h2>Crear una Cuenta</h2>

            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="registro.php" method="POST">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellidos" placeholder="Apellidos" required>
                <input type="email" name="email" placeholder="Correo electronico" required>
                <input type="password" name="password" placeholder="Contrasena" required minlength="6">
                <button type="submit">Registrarse</button>
            </form>

            <div class="footer-links">
                Ya tienes cuenta? <a href="login.php">Inicia sesion aqui</a>
            </div>
            <a href="index.html?v=8" class="btn-volver-inicio">Volver a inicio</a>
        </div>
    </main>

    <script src="js/auth.js?v=8"></script>
</body>
</html>
