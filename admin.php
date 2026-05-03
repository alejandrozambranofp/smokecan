<?php
session_start();
require 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.html");
    exit();
}

// Obtener estadísticas
$total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuario")->fetch_assoc()['total'];
$total_comentarios = $conn->query("SELECT COUNT(*) as total FROM comentarios")->fetch_assoc()['total'];
$total_zonas = $conn->query("SELECT COUNT(*) as total FROM zonas_usuarios")->fetch_assoc()['total'];

// Obtener usuarios
$usuarios = $conn->query("SELECT id, nombre, apellidos, email, rol, fecha_registro FROM usuario ORDER BY fecha_registro DESC");

// Obtener comentarios
$comentarios = $conn->query("SELECT * FROM comentarios ORDER BY fecha DESC");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Smokecan</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-main { padding: 40px 5%; background: #f4f7f6; min-height: 100vh; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; }
        .stat-card i { font-size: 2em; color: #00796B; margin-bottom: 10px; }
        .stat-card h3 { font-size: 0.9em; color: #666; margin: 0; }
        .stat-card p { font-size: 1.8em; font-weight: bold; color: #2c3e50; margin: 5px 0 0 0; }
        
        .admin-section { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 40px; }
        .admin-section h2 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 12px; background: #f8f9fa; color: #2c3e50; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #eee; color: #555; font-size: 0.9em; }
        tr:hover { background: #fcfcfc; }
        
        .badge { padding: 4px 8px; border-radius: 10px; font-size: 0.8em; font-weight: bold; }
        .badge-admin { background: #e3f2fd; color: #1976d2; }
        .badge-user { background: #f5f5f5; color: #757575; }
        
        .btn-edit { background: #e8f5e9; color: #2e7d32; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; transition: 0.2s; margin-right: 5px; }
        .btn-edit:hover { background: #2e7d32; color: white; }

        /* Modal Edit */
        .modal-edit { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-edit-content { background: white; margin: 5% auto; padding: 30px; border-radius: 20px; width: 400px; box-shadow: 0 5px 25px rgba(0,0,0,0.2); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #666; font-size: 0.9em; }
        .form-group input { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-save { background: #00796B; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
        .btn-cancel { background: #eee; color: #666; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }

        .role-select {
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            color: #333;
            font-size: 0.9em;
            cursor: pointer;
            outline: none;
            transition: 0.2s;
        }
        .role-select:focus { border-color: #00796B; background: white; }
    </style>
</head>
<body>
    <header class="cabecera-contenedor">
        <div class="cabecera-principal">
            <div class="logo">
                <a href="index.html"><img src="img/smokecan-logo.png" alt="Logo SMOKECAN"></a>
            </div>
            <div></div>
        </div>
        <nav class="cabecera-navegacion">
            <div class="enlaces-nav">
                <div class="enlace-item"><a href="index.html">Mapa</a></div>
                <div class="enlace-item"><a href="foro.php">Foro</a></div>
                <div class="enlace-item"><a href="admin.php" class="activo">Panel Admin</a></div>
            </div>
        </nav>
    </header>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Panel de Control Administrativo</h1>
            <p>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa fa-users"></i>
                <h3>Usuarios</h3>
                <p><?php echo $total_usuarios; ?></p>
            </div>
            <div class="stat-card">
                <i class="fa fa-comments"></i>
                <h3>Comentarios</h3>
                <p><?php echo $total_comentarios; ?></p>
            </div>
            <div class="stat-card">
                <i class="fa fa-map-marker-alt"></i>
                <h3>Zonas Reportadas</h3>
                <p><?php echo $total_zonas; ?></p>
            </div>
        </div>

        <section class="admin-section">
            <h2>Gestión de Usuarios</h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($u = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $u['nombre'] . " " . $u['apellidos']; ?></td>
                            <td><?php echo $u['email']; ?></td>
                            <td>
                                <select onchange="cambiarRol(<?php echo $u['id']; ?>, this.value)" class="role-select">
                                    <option value="user" <?php echo $u['rol'] == 'user' ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="admin" <?php echo $u['rol'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($u['fecha_registro'])); ?></td>
                            <td>
                                <button class="btn-edit" onclick='abrirModalEditar(<?php echo json_encode($u); ?>)'>
                                    <i class="fa fa-edit"></i>
                                </button>
                                <?php if($u['id'] != $_SESSION['usuario_id']): ?>
                                <button class="btn-delete" onclick="eliminar('usuario', <?php echo $u['id']; ?>)">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-section">
            <h2>Gestión de Comentarios</h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Zona</th>
                            <th>Comentario</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($c = $comentarios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $c['usuario_nombre']; ?></td>
                            <td><b><?php echo $c['sitio']; ?></b></td>
                            <td style="max-width: 300px;"><?php echo $c['comentario']; ?></td>
                            <td>
                                <span class="badge <?php echo $c['estado'] == 'aprobado' ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo strtoupper($c['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($c['fecha'])); ?></td>
                            <td>
                                <?php if($c['estado'] == 'pendiente'): ?>
                                <button class="btn-edit" onclick="aprobarComentario(<?php echo $c['id']; ?>)" style="background:#e8f5e9; color:#2e7d32; margin-right:5px;" title="Aprobar">
                                    <i class="fa fa-check"></i>
                                </button>
                                <?php endif; ?>
                                <button class="btn-delete" onclick="eliminar('comentario', <?php echo $c['id']; ?>)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal Editar Usuario -->
    <div id="modal-editar-usuario" class="modal-edit">
        <div class="modal-edit-content">
            <h2>Editar Usuario</h2>
            <form id="form-editar-usuario">
                <input type="hidden" id="edit-id">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" id="edit-nombre" required>
                </div>
                <div class="form-group">
                    <label>Apellidos</label>
                    <input type="text" id="edit-apellidos" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="edit-email" required>
                </div>
                <div class="form-group">
                    <label>Nueva Contraseña (dejar vacío para no cambiar)</label>
                    <input type="password" id="edit-password" placeholder="********">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalEditar(usuario) {
            document.getElementById('edit-id').value = usuario.id;
            document.getElementById('edit-nombre').value = usuario.nombre;
            document.getElementById('edit-apellidos').value = usuario.apellidos;
            document.getElementById('edit-email').value = usuario.email;
            document.getElementById('edit-password').value = "";
            document.getElementById('modal-editar-usuario').style.display = "block";
        }

        function cerrarModal() {
            document.getElementById('modal-editar-usuario').style.display = "none";
        }

        document.getElementById('form-editar-usuario').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;
            const nombre = document.getElementById('edit-nombre').value;
            const apellidos = document.getElementById('edit-apellidos').value;
            const email = document.getElementById('edit-email').value;
            const password = document.getElementById('edit-password').value;

            fetch('admin_acciones.php?accion=editar_usuario', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&nombre=${nombre}&apellidos=${apellidos}&email=${email}&password=${password}`
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

        function aprobarComentario(id) {
            fetch(`admin_acciones.php?accion=aprobar_comentario&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                });
        }

        function cambiarRol(id, nuevoRol) {
            fetch(`admin_acciones.php?accion=cambiar_rol&id=${id}&rol=${nuevoRol}`)
                .then(res => res.json())
                .then(data => {
                    if(!data.success) {
                        alert("Error: " + data.message);
                        location.reload();
                    }
                });
        }

        function eliminar(tipo, id) {
            if(confirm(`¿Estás seguro de que quieres eliminar este ${tipo}? Esta acción no se puede deshacer.`)) {
                fetch(`admin_acciones.php?accion=eliminar_${tipo}&id=${id}`)
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
