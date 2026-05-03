<?php
require_once 'conexion.php';

$sql = "CREATE TABLE IF NOT EXISTS comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sitio VARCHAR(255) NOT NULL,
    valoracion INT NOT NULL,
    comentario TEXT,
    foto VARCHAR(255),
    usuario_id INT,
    usuario_nombre VARCHAR(100),
    estado ENUM('pendiente', 'aprobado') DEFAULT 'pendiente',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'comentarios' creada o ya existe.\n";
} else {
    echo "Error creando tabla: " . $conn->error . "\n";
}

$sql_usuario = "CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('user', 'admin') DEFAULT 'user',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_usuario) === TRUE) {
    echo "Tabla 'usuario' creada o ya existe.\n";
} else {
    echo "Error creando tabla usuario: " . $conn->error . "\n";
}

// Nueva tabla para zonas marcadas por usuarios
$sql_zonas_user = "CREATE TABLE IF NOT EXISTS zonas_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lat DOUBLE NOT NULL,
    lng DOUBLE NOT NULL,
    usuario_id INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
)";

if ($conn->query($sql_zonas_user) === TRUE) {
    echo "Tabla 'zonas_usuarios' creada o ya existe.\n";
} else {
    echo "Error creando tabla zonas_usuarios: " . $conn->error . "\n";
}

// Nueva tabla para votos de veracidad
$sql_votos = "CREATE TABLE IF NOT EXISTS votos_zonas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zona_id INT NOT NULL,
    usuario_id INT NOT NULL,
    voto TINYINT NOT NULL, -- 1 para SI, 0 para NO
    UNIQUE KEY (zona_id, usuario_id),
    FOREIGN KEY (zona_id) REFERENCES zonas_usuarios(id),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
)";

if ($conn->query($sql_votos) === TRUE) {
    echo "Tabla 'votos_zonas' creada o ya existe.\n";
} else {
    echo "Error creando tabla votos_zonas: " . $conn->error . "\n";
}

// Nueva tabla para zonas oficiales (migración de las harcoded)
$sql_zonas_oficiales = "CREATE TABLE IF NOT EXISTS zonas_oficiales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    lat DOUBLE NOT NULL,
    lng DOUBLE NOT NULL,
    radio INT NOT NULL,
    tipo VARCHAR(50),
    nivel VARCHAR(50) DEFAULT 'prohibido'
)";

if ($conn->query($sql_zonas_oficiales) === TRUE) {
    echo "Tabla 'zonas_oficiales' creada o ya existe.\n";
    
    // Insertar zonas oficiales si la tabla está vacía
    $check = $conn->query("SELECT COUNT(*) as total FROM zonas_oficiales");
    $row = $check->fetch_assoc();
    if ($row['total'] == 0) {
        $sql_insert = "INSERT INTO zonas_oficiales (nombre, lat, lng, radio, tipo, nivel) VALUES 
            ('Hospital de Molins', 41.4115, 2.0165, 100, 'hospital', 'prohibido'),
            ('Parc de la Mariona', 41.4145, 2.0135, 80, 'parque', 'prohibido'),
            ('Escola El Palau', 41.4125, 2.0150, 70, 'colegio', 'prohibido'),
            ('Biblioteca El Molí', 41.4155, 2.0175, 50, 'terraza', 'no_recomendado')";
        
        if ($conn->query($sql_insert) === TRUE) {
            echo "Zonas oficiales insertadas con éxito.\n";
        }
    }
} else {
    echo "Error creando tabla zonas_oficiales: " . $conn->error . "\n";
}

$conn->close();
?>
