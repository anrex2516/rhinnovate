<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'supervisor') {
    header("Location: login.php");
    exit;
}
?>
