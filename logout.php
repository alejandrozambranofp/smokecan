<?php
session_start();
session_destroy(); // Mata la sesión en el servidor

// Borra la cookie en el navegador (poniendo fecha pasada)
setcookie("usuario_logeado", "", time() - 3600, "/"); 

header("Location: index.html");
exit();
?>