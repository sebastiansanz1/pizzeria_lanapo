<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['usuario_rol'] === 'Admin') {
        header("Location: servicio-usuarios/usuarios.php");
    } else {
        header("Location: servicio-ventas/ventas.php");
    }
} else {
    header("Location: servicio-usuarios/login.php");
}
exit;
?>