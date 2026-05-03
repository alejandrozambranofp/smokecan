<?php
require_once 'conexion.php';
session_start();

// Obtener comentarios de la DB
$sql = "SELECT * FROM comentarios ORDER BY fecha DESC";
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

        <section class="foro-controles">
            <div class="foro-buscar-caja">
                <input type="text" placeholder="Buscar zona o establecimiento en Molins..." class="foro-buscar-input">
                <button class="foro-buscar-btn" aria-label="Buscar"><i class="fa fa-search"></i></button>
            </div>
            <button class="foro-filtro-btn">Filtrar</button>
            <button class="foro-publicar-btn" id="btn-abrir-modal">Publicar nuevo comentario</button>
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
                            <small style="color: #999;"><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></small>
                        </article>
                    <?php endwhile; ?>
                <?php endif; ?>

                <!-- Comentarios estáticos de ejemplo (puedes quitarlos si quieres) -->
                <article class="tarjeta-comentario">
                    <div class="comentario-header">
                        <img src="img/icono-usuario.png" alt="Usuario" class="comentario-avatar">
                        <div class="comentario-info">
                            <p class="comentario-nombre">ALEJANDROZT #2704</p>
                            <p class="comentario-zona">Terraza El Racó (Molins):</p>
                        </div>
                    </div>
                    <p class="comentario-texto">Limpia. 100% libre de humo. El personal se asegura de que se cumpla la normativa desde el primer minuto.</p>
                    <div class="comentario-estrellas">
                        <span class="star-rating" style="color: gold;">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                    </div>
                </article>

            </section>

            <aside class="foro-populares-columna">
                <h2 class="populares-titulo">Sitios Populares 🔥</h2>
                
                <div class="tarjeta-popular">
                    <img src="https://images.unsplash.com/photo-1525610553991-2bede1a236e2?auto=format&fit=crop&w=300&q=80" alt="Passeig de Pi i Margall" class="popular-imagen">
                    <div class="popular-info">
                        <p class="popular-nombre">Passeig de Pi i Margall</p>
                        <div class="popular-estrellas">
                            <span class="star-rating" style="color: gold;">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                    </div>
                    <button class="popular-btn">ver</button>
                </div>

                <div class="tarjeta-popular">
                    <img src="https://images.unsplash.com/photo-1604928141064-207cea6f5722?auto=format&fit=crop&w=300&q=80" alt="Parc de la Mariona" class="popular-imagen">
                    <div class="popular-info">
                        <p class="popular-nombre">Parc de la Mariona</p>
                        <div class="popular-estrellas">
                            <span class="star-rating" style="color: gold;">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
                        </div>
                    </div>
                    <button class="popular-btn">ver</button>
                </div>
            </aside>
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

        // Lógica de Búsqueda en el Foro
        const buscarInput = document.querySelector('.foro-buscar-input');
        const buscarBtn = document.querySelector('.foro-buscar-btn');
        const filtroBtn = document.querySelector('.foro-filtro-btn');
        const comentarios = document.querySelectorAll('.tarjeta-comentario');

        const filtrarComentarios = () => {
            const term = buscarInput.value.toLowerCase().trim();
            comentarios.forEach(c => {
                const texto = c.innerText.toLowerCase();
                c.style.display = texto.includes(term) ? "block" : "none";
            });
        };

        if (buscarBtn) buscarBtn.onclick = filtrarComentarios;
        if (buscarInput) {
            buscarInput.onkeypress = (e) => { if(e.key === 'Enter') filtrarComentarios(); }
        }
        if (filtroBtn) {
            filtroBtn.onclick = () => {
                buscarInput.value = "";
                comentarios.forEach(c => c.style.display = "block");
            };
        }

        // Lógica para botones "ver" de populares
        document.querySelectorAll('.popular-btn').forEach(btn => {
            btn.onclick = function() {
                const nombre = this.parentElement.querySelector('.popular-nombre').innerText;
                buscarInput.value = nombre;
                filtrarComentarios();
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
