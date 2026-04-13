<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Smokecan</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="cabecera-contenedor">
        <div class="cabecera-principal">
            <div class="logo"><a href="index.html"><img src="img/smokecan-logo.png" alt="Logo"></a></div>
            <nav class="enlaces-nav">
                <a href="index.html" class="enlace-item">Mapa</a>
                <a href="perfil.php" class="enlace-item activo">Mi Perfil</a>
                <a href="index.html" class="enlace-item">Atras</a>
            </nav>
        </div>
    </header>

    <main class="perfil-main">
        <div class="perfil-card">
            <div class="avatar-edit">
                <img src="img/icono-usuario.png" alt="Avatar">
            </div>
            <h2>Hola, <?php echo $_SESSION['nombre']; ?></h2>
            <p>ID de usuario: #<?php echo $_SESSION['usuario_id']; ?></p>
            <form class="perfil-formulario">
                <div class="campo">
                    <label>Email</label>
                    <input type="text" value="<?php echo $_SESSION['email']; ?>" readonly>
                </div>
                <div class="perfil-acciones">
                    <button type="button" class="btn-guardar">Actualizar Datos</button>
                    <a href="logout.php" class="btn-logout" style="text-decoration:none; display:block; margin-top:10px;">Cerrar Sesión</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>