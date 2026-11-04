<?php
    require_once "../../config/conexion.php"; // conexion.php ya contiene session_start()
    session_unset(); // Elimina todas las variables de la sesión
    session_destroy(); // Destruye la sesión actual
    header('Location: ' . \Conectar::ruta() . "index.php"); // Redirige al login
    exit();
?>