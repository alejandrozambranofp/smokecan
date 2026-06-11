<?php
require_once 'conexion.php';
session_start();

$sql = "SELECT c.*, u.avatar FROM comentarios c LEFT JOIN usuario u ON c.usuario_id = u.id WHERE c.estado = 'aprobado' ORDER BY c.fecha DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro SmokeCan: Opiniones por zones</title>
    <link rel="icon" type="image/png" href="img/smokecan-logo.svg">
    <link rel="stylesheet" href="css/style.css?v=8"> 
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
                <div class="enlace-item"><a href="foro.php" class="activo">Foro Smokecan</a></div>
                <div class="enlace-item"><a href="estancos.html">Estancos</a></div>
                <div class="enlace-item" id="nav-perfil-link"><a href="login.php">Mi Perfil</a></div>
            </div>
        </nav>
    </header>

    <main class="foro-main">
        <h1 class="foro-titulo">Foro SmokeCan: Opiniones por zonas</h1>

        <section class="foro-controles" style="justify-content: center; gap: 15px; flex-wrap: wrap;">
            <button class="foro-publicar-btn" id="btn-abrir-modal-humo" style="flex: 1; min-width: 200px; background:#00796B;">
                <i class="fa-solid fa-leaf"></i> Opinar Zona Libre de Humo
            </button>
            <button class="foro-publicar-btn" id="btn-abrir-modal-fumar" style="flex: 1; min-width: 200px; background:#FF6B35;">
                <i class="fa-solid fa-smoking"></i> Opinar Zona para Fumar
            </button>
        </section>

        <div id="modal-publicar-humo" class="modal">
            <div class="modal-contenido">
                <span class="cerrar-modal">&times;</span>
                <h2><i class="fa-solid fa-leaf"></i> Publicar Valoración - Zona Libre de Humo</h2>
                <form action="publicar_comentario.php" method="POST" enctype="multipart/form-data" class="foro-formulario">
                    <input type="hidden" name="tipo_zona" value="libre_humo">
                    <div class="campo">
                        <label for="sitio-humo">Nombre del sitio:</label>
                        <select name="sitio" id="sitio-humo" required>
                            <option value="">-- Selecciona una zona --</option>
                        </select>
                    </div>
                    
                    <div class="campo">
                        <label>Valoración:</label>
                        <div class="rating-input">
                            <input type="radio" name="valoracion" value="5" id="star5"><label for="star5">★</label>
                            <input type="radio" name="valoracion" value="4" id="star4"><label for="star4">★</label>
                            <input type="radio" name="valoracion" value="3" id="star3"><label for="star3">★</label>
                            <input type="radio" name="valoracion" value="2" id="star2"><label for="star2">★</label>
                            <input type="radio" name="valoracion" value="1" id="star1" required><label for="star1">★</label>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="comentario">Comentario (opcional):</label>
                        <textarea name="comentario" id="comentario" rows="3" placeholder="Cuéntanos tu experiencia..."></textarea>
                    </div>

                    <div class="campo">
                        <label for="foto">Añadir foto (opcional):</label>
                        <input type="file" name="foto" id="foto" accept="image/*">
                    </div>

                    <button type="submit" class="btn-guardar">Publicar ahora</button>
                </form>
            </div>
        </div>

        <div id="modal-publicar-fumar" class="modal">
            <div class="modal-contenido">
                <span class="cerrar-modal">&times;</span>
                <h2><i class="fa-solid fa-smoking"></i> Publicar Valoración - Zona para Fumar</h2>
                <form action="publicar_comentario.php" method="POST" enctype="multipart/form-data" class="foro-formulario">
                    <input type="hidden" name="tipo_zona" value="para_fumar">
                    <div class="campo">
                        <label for="sitio-fumar">Nombre del sitio:</label>
                        <select name="sitio" id="sitio-fumar" required>
                            <option value="">-- Selecciona una zona --</option>
                        </select>
                    </div>
                    
                    <div class="campo">
                        <label>Valoración:</label>
                        <div class="rating-input">
                            <input type="radio" name="valoracion" value="5" id="star5-fumar"><label for="star5-fumar">★</label>
                            <input type="radio" name="valoracion" value="4" id="star4-fumar"><label for="star4-fumar">★</label>
                            <input type="radio" name="valoracion" value="3" id="star3-fumar"><label for="star3-fumar">★</label>
                            <input type="radio" name="valoracion" value="2" id="star2-fumar"><label for="star2-fumar">★</label>
                            <input type="radio" name="valoracion" value="1" id="star1-fumar" required><label for="star1-fumar">★</label>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="comentario-fumar">Comentario (opcional):</label>
                        <textarea name="comentario" id="comentario-fumar" rows="3" placeholder="Cuéntanos tu experiencia..."></textarea>
                    </div>

                    <div class="campo">
                        <label for="foto-fumar">Añadir foto (opcional):</label>
                        <input type="file" name="foto" id="foto-fumar" accept="image/*">
                    </div>

                    <button type="submit" class="btn-guardar">Publicar ahora</button>
                </form>
            </div>
        </div>

        <div class="foro-contenido-grid">
            <section class="foro-comentarios-columna">
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while($row = $resultado->fetch_assoc()): ?>
                        <article class="tarjeta-comentario">
                            <div class="comentario-header">
                                <img src="<?php echo !empty($row['avatar']) ? htmlspecialchars($row['avatar']) : 'img/icono-usuario.png'; ?>" alt="Usuario" class="comentario-avatar">
                                <div class="comentario-info">
                                    <p class="comentario-nombre"><?php echo htmlspecialchars($row['usuario_nombre']); ?></p>
                                    <p class="comentario-zona"><?php echo htmlspecialchars($row['sitio']); ?>:</p>
                                </div>
                            </div>
                            <?php if ($row['comentario']): ?>
                                <p class="comentario-texto"><?php echo htmlspecialchars($row['comentario']); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($row['foto']): ?>
                                <div class="comentario-imagen-adjunta">
                                    <img src="<?php echo $row['foto']; ?>" alt="Foto del sitio" style="max-width: 100%; border-radius: 8px; margin: 10px 0;">
                                </div>
                            <?php endif; ?>

                            <div class="comentario-estrellas">
                                <span class="star-rating" style="color: gold;">
                                    <?php 
                                    for ($i=1; $i<=5; $i++) {
                                        echo ($i <= $row['valoracion']) ? '&#9733;' : '&#9734;';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                                <small style="color: #999;"><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></small>
                                <a href="index.html?zona=<?php echo htmlspecialchars($row['sitio']); ?>&v=8" class="btn-ver-mapa" style="background:#00796B; color:white; padding:5px 10px; border-radius:15px; text-decoration:none; font-size:11px; display:flex; align-items:center; gap:5px;">
                                    <i class="fa fa-map-marker-alt"></i> Ver en mapa
                                </a>
                                <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $row['usuario_id']): ?>
                                <div class="comentario-acciones-autor">
                                    <button onclick="eliminarComentario(<?php echo $row['id']; ?>)" class="btn-borrar-coment" title="Eliminar"><i class="fa fa-trash"></i></button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php endif; ?>
            </section>
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

    <script src="js/auth.js?v=8"></script>
    <script>
        function cargarZonasEnSelect(tipo, selectId) {
            fetch('obtener_zonas_nombres_tipo.php?tipo=' + tipo)
                .then(res => res.json())
                .then(zonas => {
                    const select = document.getElementById(selectId);
                    while (select.options.length > 1) {
                        select.remove(1);
                    }
                    zonas.forEach(zona => {
                        const option = document.createElement('option');
                        option.value = zona.nombre_sitio;
                        option.textContent = zona.nombre_sitio + ' (' + zona.autor + ')';
                        select.appendChild(option);
                    });
                });
        }

        const modalHumo = document.getElementById("modal-publicar-humo");
        const modalFumar = document.getElementById("modal-publicar-fumar");
        const btnHumo = document.getElementById("btn-abrir-modal-humo");
        const btnFumar = document.getElementById("btn-abrir-modal-fumar");
        const closeButtons = document.getElementsByClassName("cerrar-modal");

        if (btnHumo) {
            btnHumo.onclick = function() { 
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    modalHumo.style.display = "block";
                    cargarZonasEnSelect('libre_humo', 'sitio-humo');
                <?php else: ?>
                    window.location.href = 'login.php';
                <?php endif; ?>
            }
        }

        if (btnFumar) {
            btnFumar.onclick = function() { 
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    modalFumar.style.display = "block";
                    cargarZonasEnSelect('para_fumar', 'sitio-fumar');
                <?php else: ?>
                    window.location.href = 'login.php';
                <?php endif; ?>
            }
        }

        for (let i = 0; i < closeButtons.length; i++) {
            closeButtons[i].onclick = function() { 
                modalHumo.style.display = "none";
                modalFumar.style.display = "none";
            }
        }

        window.onclick = function(event) {
            if (event.target == modalHumo) modalHumo.style.display = "none";
            if (event.target == modalFumar) modalFumar.style.display = "none";
        }

        function eliminarComentario(id) {
            if(confirm("¿Seguro que quieres borrar tu comentario?")) {
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
</body>
</html>
<?php $conn->close(); ?>
