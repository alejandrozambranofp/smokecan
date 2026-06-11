<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['usuario_id'];

$check_col = $conn->query("SHOW COLUMNS FROM usuario LIKE 'avatar'");
if ($check_col->num_rows == 0) {
    $conn->query("ALTER TABLE usuario ADD COLUMN avatar VARCHAR(255) DEFAULT 'img/icono-usuario.png'");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_avatar') {
    header('Content-Type: application/json');
    if (!isset($_POST['avatar'])) {
        echo json_encode(['success' => false, 'message' => 'Falta el avatar']);
        exit();
    }
    
    $avatar = $conn->real_escape_string($_POST['avatar']);
    $avatares_validos = [
        'img/avatares/avatar-hoja.webp',
        'img/avatares/avatar-lata.webp',
        'img/avatares/avatar-nube.webp',
        'img/avatares/avatar-pipa.webp'
    ];
    
    if (!in_array($avatar, $avatares_validos)) {
        echo json_encode(['success' => false, 'message' => 'Avatar no valido']);
        exit();
    }
    
    $sql_update = "UPDATE usuario SET avatar = '$avatar' WHERE id = $uid";
    if ($conn->query($sql_update)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    exit();
}

$user_data = $conn->query("SELECT avatar FROM usuario WHERE id = $uid")->fetch_assoc();
$avatar_actual = $user_data['avatar'] ?: 'img/icono-usuario.png';

$mis_comentarios = $conn->query("SELECT * FROM comentarios WHERE usuario_id = $uid ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Smokecan</title>
    <link rel="icon" type="image/png" href="img/smokecan-logo.svg">
    <link rel="stylesheet" href="css/style.css?v=8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .perfil-grid {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        @media (min-width: 851px) {
            .perfil-grid {
                display: grid;
                grid-template-columns: 1fr 2fr;
                align-items: start;
            }
        }
    </style>
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
                <div class="enlace-item" id="nav-perfil-link"><a href="perfil.php" class="activo">Mi Perfil</a></div>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <div class="enlace-item"><a href="admin.php">Panel Admin</a></div>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="foro-main">
        <h1 class="foro-titulo">Mi Perfil</h1>

        <div class="perfil-grid">
            <div class="perfil-columna-usuario">
                <div class="perfil-card" style="max-width: 100%; box-sizing: border-box;">
                    <div class="avatar-edit">
                        <img id="avatar-img-principal" src="<?php echo htmlspecialchars($avatar_actual); ?>" alt="Avatar" style="border: 3px solid #00796B; border-radius: 50%; width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    
                    <h3 style="color: #2c3e50; font-size: 1.1em; margin-top: 25px; margin-bottom: 15px;">Selecciona tu avatar:</h3>
                    <div class="selector-avatares" style="display: flex; gap: 10px; justify-content: center; margin-bottom: 20px;">
                        <?php
                        $directorio_avatares = 'img/avatares/';
                        $avatares_disponibles = ['avatar-hoja.webp', 'avatar-lata.webp', 'avatar-nube.webp', 'avatar-pipa.webp'];
                        foreach ($avatares_disponibles as $av) {
                            $ruta_av = $directorio_avatares . $av;
                            $seleccionado = ($avatar_actual === $ruta_av) ? 'border: 3px solid #00796B; transform: scale(1.1);' : 'border: 1px solid #ddd; cursor: pointer;';
                            echo '<img class="opcion-avatar" src="' . $ruta_av . '" data-avatar="' . $ruta_av . '" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; transition: 0.2s; ' . $seleccionado . '" onclick="seleccionarAvatar(this)">';
                        }
                        ?>
                    </div>

                    <h2 style="color: #2c3e50;">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h2>
                    <p style="color: #666; font-size: 0.9em; margin-bottom: 20px;">ID de usuario: #<?php echo $_SESSION['usuario_id']; ?></p>
                    <div class="perfil-formulario" style="text-align: left;">
                        <div class="campo">
                            <label style="font-weight: bold; color: #2c3e50; display: block; margin-bottom: 5px;">Email</label>
                            <input type="text" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; background: #f9f9f9; color: #555;">
                        </div>
                        <div class="perfil-acciones" style="margin-top: 25px;">
                            <a href="logout.php" class="foro-publicar-btn" style="text-decoration: none; display: block; text-align: center; background: #d32f2f; box-shadow: 0 4px 10px rgba(211, 47, 47, 0.3);">
                                <i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="perfil-columna-comentarios">
                <div class="perfil-comentarios-seccion" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); box-sizing: border-box;">
                    <h2 style="color: #2c3e50; border-bottom: 2px solid #e0e0e0; padding-bottom: 15px; margin-top: 0; margin-bottom: 20px;">Mis Comentarios en el Foro</h2>
                    
                    <?php if ($mis_comentarios->num_rows > 0): ?>
                        <div class="foro-comentarios-columna">
                            <?php while($c = $mis_comentarios->fetch_assoc()): ?>
                                <article class="tarjeta-comentario" style="border-left: 5px solid <?php echo $c['estado'] == 'aprobado' ? '#00796B' : '#ffa000'; ?>;">
                                    <div class="comentario-header">
                                        <img src="<?php echo htmlspecialchars($avatar_actual); ?>" alt="Usuario" class="comentario-avatar">
                                        <div class="comentario-info">
                                            <p class="comentario-nombre">
                                                <?php echo htmlspecialchars($c['sitio']); ?>
                                                <span class="badge" style="background:<?php echo $c['estado'] == 'aprobado' ? '#e8f5e9' : '#fff3e0'; ?>; color:<?php echo $c['estado'] == 'aprobado' ? '#2e7d32' : '#ef6c00'; ?>; font-size:0.7em; padding:3px 8px; border-radius:10px; margin-left:5px; font-weight: bold; display: inline-block; vertical-align: middle;">
                                                    <?php echo strtoupper($c['estado']); ?>
                                                </span>
                                            </p>
                                            <p class="comentario-zona"><?php echo date('d/m/Y H:i', strtotime($c['fecha'])); ?></p>
                                        </div>
                                    </div>
                                    <p class="comentario-texto"><?php echo htmlspecialchars($c['comentario']); ?></p>
                                    <div style="display:flex; gap:15px; justify-content:flex-end; align-items:center; margin-top: 15px; border-top: 1px solid #f5f5f5; padding-top: 10px;">
                                        <button onclick="editarMiComentario(<?php echo $c['id']; ?>, '<?php echo addslashes($c['comentario']); ?>')" style="background:none; border:none; color:#00796B; cursor:pointer; font-size:0.85em; font-weight:bold; display:flex; align-items:center; gap:5px;"><i class="fa fa-edit"></i> Editar</button>
                                        <button onclick="eliminarMiComentario(<?php echo $c['id']; ?>)" style="background:none; border:none; color:#d32f2f; cursor:pointer; font-size:0.85em; font-weight:bold; display:flex; align-items:center; gap:5px;"><i class="fa fa-trash"></i> Borrar</button>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p style="color:#666; font-style:italic; text-align:center; padding: 20px 0;">Aún no has publicado ningún comentario.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer-contenedor">
        <div class="footer-columnas">
            <div class="footer-columna">
                <h3 class="footer-titulo-principal">Smokecan</h3>
                <p class="footer-texto">Tu guía de zonas libres de humo</p>
            </div>
            <div class="footer-columna">
                <h3 class="footer-titulo">Información importante</h3>
                <ul class="footer-lista">
                    <li><a href="#">Sobre la nueva Ley</a></li>
                    <li><a href="#">Política de Privacidad</a></li>
                    <li><a href="#">Aviso legal</a></li>
                    <li><a href="#">Términos de Uso</a></li>
                </ul>
            </div>
            <div class="footer-columna">
                <h3 class="footer-titulo">Ayuda y Contacto</h3>
                <p class="footer-texto">Contacto: smokecancompany@gmail.com</p>
            </div>
        </div>
        <div class="footer-copyright">
            <p><strong>Smokecan</strong> ©2026 Smokecan | Todos los derechos reservados</p>
        </div>
    </footer>

    <div id="modal-editar-comentario" class="modal">
        <div class="modal-contenido">
            <span class="cerrar-modal" onclick="cerrarModal()">&times;</span>
            <h2 style="color: #2c3e50; margin-top: 0;"><i class="fa-solid fa-edit" style="color: #00796B;"></i> Editar Comentario</h2>
            <p style="font-size:0.85em; color:#ffa000; margin-bottom:20px; font-weight: 500;"><i class="fa fa-info-circle"></i> Al editarlo, el comentario volverá a estar pendiente de aprobación.</p>
            <form id="form-editar-comentario" class="foro-formulario">
                <input type="hidden" id="edit-coment-id">
                <div class="campo">
                    <label style="font-weight: bold; color: #2c3e50; display: block; margin-bottom: 8px;">Tu comentario</label>
                    <textarea id="edit-coment-texto" rows="4" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: inherit; font-size: 14px;"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="foro-filtro-btn" onclick="cerrarModal()" style="border-radius: 25px; padding: 12px 25px;">Cancelar</button>
                    <button type="submit" class="foro-publicar-btn" style="border-radius: 25px; padding: 12px 25px;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function seleccionarAvatar(elemento) {
            const rutaAvatar = elemento.getAttribute('data-avatar');

            fetch('perfil.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `accion=actualizar_avatar&avatar=${encodeURIComponent(rutaAvatar)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('avatar-img-principal').src = rutaAvatar;

                    const headerImg = document.querySelector('.usuario-circulo img');
                    if (headerImg) {
                        headerImg.src = rutaAvatar;
                    }

                    document.querySelectorAll('.opcion-avatar').forEach(img => {
                        img.style.border = '1px solid #ddd';
                        img.style.transform = 'none';
                    });
                    elemento.style.border = '3px solid #00796B';
                    elemento.style.transform = 'scale(1.1)';
                } else {
                    alert("Error: " + data.message);
                }
            });
        }

        function cerrarModal() {
            document.getElementById('modal-editar-comentario').style.display = "none";
        }

        function editarMiComentario(id, texto) {
            document.getElementById('edit-coment-id').value = id;
            document.getElementById('edit-coment-texto').value = texto;
            document.getElementById('modal-editar-comentario').style.display = "block";
        }

        document.getElementById('form-editar-comentario').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('edit-coment-id').value;
            const texto = document.getElementById('edit-coment-texto').value;

            fetch('comentarios_acciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `accion=editar&id=${id}&comentario=${encodeURIComponent(texto)}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            });
        };

        function eliminarMiComentario(id) {
            if(confirm("¿Seguro que quieres borrar este comentario?")) {
                fetch('comentarios_acciones.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `accion=borrar&id=${id}`
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                });
            }
        }
    </script>
    <script src="js/auth.js?v=8"></script>
</body>
</html>
