<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['usuario_id'];
$mis_comentarios = $conn->query("SELECT * FROM comentarios WHERE usuario_id = $uid ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Smokecan</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .perfil-main { padding: 40px 5%; max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 40px; }
        .perfil-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); text-align: center; }
        .perfil-comentarios-seccion { background: #f8f9fa; padding: 30px; border-radius: 20px; border: 1px solid #eee; }
        
        .tarjeta-comentario-perfil { 
            background: white; 
            padding: 20px; 
            border-radius: 15px; 
            margin-bottom: 20px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: transform 0.2s;
        }
        .tarjeta-comentario-perfil:hover { transform: translateY(-3px); }

        .modal-edit { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); }
        .modal-edit-content { background: white; margin: 10% auto; padding: 30px; border-radius: 25px; width: 90%; max-width: 500px; box-shadow: 0 15px 40px rgba(0,0,0,0.3); }
        
        @media (max-width: 600px) {
            .perfil-main { padding: 20px 15px; }
            .perfil-card, .perfil-comentarios-seccion { padding: 20px; }
            .btn-save, .btn-cancel { width: 100%; }
            .modal-footer { flex-direction: column; }
        }

        .btn-save { background: #00796B; color: white; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-save:hover { background: #004d40; transform: scale(1.02); }
        .btn-cancel { background: #f5f5f5; color: #666; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; font-weight: bold; }
    </style>
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
            <h2>Hola, <?php echo $_SESSION['usuario_nombre']; ?></h2>
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
        <div class="perfil-comentarios-seccion" style="margin-top: 40px;">
            <h2 style="color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Mis Comentarios en el Foro</h2>
            
            <?php if ($mis_comentarios->num_rows > 0): ?>
                <div class="mis-comentarios-grid">
                    <?php while($c = $mis_comentarios->fetch_assoc()): ?>
                        <div class="tarjeta-comentario-perfil" style="background:white; padding:15px; border-radius:12px; margin-bottom:15px; box-shadow:0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid <?php echo $c['estado'] == 'aprobado' ? '#00796B' : '#ffa000'; ?>;">
                            <div style="display:flex; justify-content:space-between; align-items:start;">
                                <div>
                                    <b style="color:#00796B;"><?php echo htmlspecialchars($c['sitio']); ?></b>
                                    <span class="badge" style="background:<?php echo $c['estado'] == 'aprobado' ? '#e8f5e9' : '#fff3e0'; ?>; color:<?php echo $c['estado'] == 'aprobado' ? '#2e7d32' : '#ef6c00'; ?>; font-size:0.7em; padding:3px 8px; border-radius:10px; margin-left:5px;">
                                        <?php echo strtoupper($c['estado']); ?>
                                    </span>
                                </div>
                                <small style="color:#999;"><?php echo date('d/m/Y', strtotime($c['fecha'])); ?></small>
                            </div>
                            <p style="color:#555; margin:10px 0; font-size:0.9em;"><?php echo htmlspecialchars($c['comentario']); ?></p>
                            <div style="display:flex; gap:10px; justify-content:flex-end;">
                                <button onclick="editarMiComentario(<?php echo $c['id']; ?>, '<?php echo addslashes($c['comentario']); ?>')" style="background:none; border:none; color:#00796B; cursor:pointer; font-size:0.85em;"><i class="fa fa-edit"></i> Editar</button>
                                <button onclick="eliminarMiComentario(<?php echo $c['id']; ?>)" style="background:none; border:none; color:#d32f2f; cursor:pointer; font-size:0.85em;"><i class="fa fa-trash"></i> Borrar</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="color:#666; font-style:italic;">Aún no has publicado ningún comentario.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal Editar Comentario -->
    <div id="modal-editar-comentario" class="modal-edit">
        <div class="modal-edit-content">
            <h2>Editar Comentario</h2>
            <p style="font-size:0.8em; color:#ffa000; margin-bottom:15px;"><i class="fa fa-info-circle"></i> Al editarlo, el comentario volverá a estar pendiente de aprobación.</p>
            <form id="form-editar-comentario">
                <input type="hidden" id="edit-coment-id">
                <div class="form-group">
                    <label>Tu comentario</label>
                    <textarea id="edit-coment-texto" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" rows="4" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
</body>
</html>