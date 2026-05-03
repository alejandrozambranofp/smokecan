<?php
require 'conexion.php';

// 1. Añadir usuario_id
$conn->query("ALTER TABLE comentarios ADD COLUMN usuario_id INT AFTER foto");

// 2. Añadir estado
$conn->query("ALTER TABLE comentarios ADD COLUMN estado ENUM('pendiente', 'aprobado') DEFAULT 'pendiente' AFTER usuario_nombre");

// 3. Añadir FK
$conn->query("ALTER TABLE comentarios ADD CONSTRAINT fk_usuario_coment FOREIGN KEY (usuario_id) REFERENCES usuario(id)");

// 4. (Opcional) Aprobar todos los existentes para no romper el foro actual
$conn->query("UPDATE comentarios SET estado = 'aprobado' WHERE estado IS NULL OR estado = ''");

echo "Tabla de comentarios actualizada para moderación.";
$conn->close();
?>
