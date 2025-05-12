<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirige al login si no está autenticado
    exit;
}

$usuario = $_SESSION['usuario']; // Obtiene los datos del usuario desde la sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados</title>
    <link rel="stylesheet" href="empleados.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="#">
                    <img src="logo.png" alt="Logo" class="logo-img">
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="../index.html" style="color:white; text-decoration:none;">Volver al inicio</a></li>
                <li><a href="#" onclick="mostrarHorario()">Horarios</a></li>
                <li><a href="#" onclick="mostrarPermisos()">Solicitar Permisos</a></li>
                <li><a href="#" onclick="limpiarSeccion()">Capacitaciones</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <div class="left-section" id="left-section"></div>
        <div class="right-section">
            <div class="profile-card">
                <h2>Perfil del Empleado</h2>
                <br>
                <br>
                <br>
                <br>
                <div class="profile-info">
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                    <br>
                    <br>
                    <br>
                
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
