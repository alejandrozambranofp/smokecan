<?php
session_start();
session_destroy();

setcookie("usuario_logeado", "", time() - 3600, "/");
setcookie("usuario_rol", "", time() - 3600, "/");

header("Location: index.html?v=6");
exit();
?>
