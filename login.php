<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.html?v=6");
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

            header("Location: index.html?v=6");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmokeCan</title>
    <link rel="stylesheet" href="css/style.css?v=8">
    <link rel="stylesheet" href="css/estilo_login.css?v=4">
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
            <h2>Iniciar Sesion</h2>
            <?php if (!empty($error)): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Correo electronico" required>
                <input type="password" name="password" placeholder="Contrasena" required>
                <button type="submit">Entrar</button>
            </form>
            <a href="index.html?v=8" class="btn-volver-inicio">Volver a inicio</a>
            <div class="footer-links">No tienes cuenta? <a href="registro.php">Registrate aqui</a></div>
        </div>
    </main>

    <script src="js/auth.js?v=8"></script>
</body>
</html>
