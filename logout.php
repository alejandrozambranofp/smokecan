<?php
session_start();
session_destroy();
header("Location: index.html"); // O a login.php
exit();
?>