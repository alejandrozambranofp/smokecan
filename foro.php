<?php
require_once 'conexion.php';
session_start();

// Obtener solo comentarios aprobados de la DB
$sql = "SELECT * FROM comentarios WHERE estado = 'aprobado' ORDER BY fecha DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro SmokeCan: Opiniones por zonas</title>
    <link rel="icon" type="image/png" href="img/smokecan-logo.png">
    
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    
    <header class="cabecera-contenedor">
        <div class="cabecera-principal">
            <div></div>
            <div class="logo">
                <a href="index.html"><img src="img/smokecan-logo.png" alt="Logo SMOKECAN"></a>
            </div>
            
            <div class="icono icono-usuario">
                <div class="usuario-circulo">
                    <a href="login.php"> <img src="img/icono-usuario.png" alt="Usuario">
                    </a>
                </div>
            </div>
        </div>
        <nav class="cabecera-navegacion">
            <div class="enlaces-nav">
                <div class="enlace-item"><a href="index.html">Mapa</a></div>
                <div class="enlace-item"><a href="foro.php" class="activo">Foro Smokecan</a></div>
                <div class="enlace-item"><a href="estancos.html">Estancos</a></div>
            </div>
        </nav>
    </header>

    <main class="foro-main">
        <h1 class="foro-titulo">Foro SmokeCan: Opiniones por zonas</h1>

        <section class="foro-controles" style="justify-content: center;">
            <button class="foro-publicar-btn" id="btn-abrir-modal" style="width: 100%; max-width: 400px;">Publicar nuevo comentario</button>
        </section>

        <!-- Modal de Publicación -->
        <div id="modal-publicar" class="modal">
            <div class="modal-contenido">
                <span class="cerrar-modal">&times;</span>
                <h2>Publicar Valoración</h2>
                <form action="publicar_comentario.php" method="POST" enctype="multipart/form-data" class="foro-formulario">
                    <div class="campo">
                        <label for="sitio">Nombre del sitio:</label>
                        <input type="text" name="sitio" id="sitio" required placeholder="Ej: Parc de la Mariona">
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

        <div class="foro-contenido-grid">
            
            <section class="foro-comentarios-columna">
                
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while($row = $resultado->fetch_assoc()): ?>
                        <article class="tarjeta-comentario">
                            <div class="comentario-header">
                                <img src="img/icono-usuario.png" alt="Usuario" class="comentario-avatar">
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
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <small style="color: #999;"><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></small>
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

            </section>

            <!-- Sección de populares eliminada -->
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
            <div class="footer-columna">
                <h3 class="footer-titulo">Redes Sociales</h3>
                <div class="redes-sociales">
                    <a href="#" aria-label="Instagram" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="X (Twitter)" class="social-link"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" aria-label="Facebook" class="social-link"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" aria-label="LinkedIn" class="social-link"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#" aria-label="TikTok" class="social-link"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <p><strong>Smokecan</strong> ©2026 Smokecan | Todos los derechos reservados</p>
        </div>
    </footer>

    <script src="js/auth.js"></script>
    <script>
        // Lógica del Modal
        const modal = document.getElementById("modal-publicar");
        const btn = document.getElementById("btn-abrir-modal");
        const span = document.getElementsByClassName("cerrar-modal")[0];

        if (btn) {
            btn.onclick = function() { modal.style.display = "block"; }
        }
        if (span) {
            span.onclick = function() { modal.style.display = "none"; }
        }
        window.onclick = function(event) {
            if (event.target == modal) modal.style.display = "none";
        }

        // Lógica de Publicar Comentario ya integrada arriba en el script del modal

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
