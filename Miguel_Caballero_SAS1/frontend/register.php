<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que los datos del formulario se reciban correctamente
    $nombre_completo = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $correo = isset($_POST['email']) ? $_POST['email'] : '';
    $contrasena = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($nombre_completo) && !empty($correo) && !empty($contrasena)) {
        // Encriptar la contraseña antes de guardarla en la base de datos
        $contrasena_encriptada = password_hash($contrasena, PASSWORD_DEFAULT);

        // Preparar la consulta SQL para insertar el nuevo usuario
        $sql = "INSERT INTO usuarios (nombre_completo, correo, contrasena, rol) VALUES (?, ?, ?, 'usuario')";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $nombre_completo, $correo, $contrasena_encriptada);

            // Ejecutar la consulta y verificar si se insertó correctamente
            if ($stmt->execute()) {
                echo "Usuario registrado exitosamente.";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Cerrar la declaración
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }

        // Cerrar la conexión
        $conn->close();
    } else {
        echo "Por favor, complete todos los campos del formulario.";
    }
}
?>


<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="css/rhlogo.jpg" type="image/x-icon">
</head>
<body>
<div class="box">
    <form action="register.php" method="post">
        <h2>𖡌 REGISTRARSE 𖡌</h2>
        <Label class="Rh">Únete a RH Innovate</Label>

        <div class="inputBox">
            <i class="fas fa-user"></i>
            <input type="text" name="fullname" required>
            <label>Nombre Completo:</label>
        </div>

        <div class="inputBox">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" required>
            <label>Correo Electrónico:</label>
        </div>

        <div class="inputBox">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" required>
            <label>Contraseña:</label>
        </div>



        <div class="links">
            <a href="login.php">¿Ya tienes cuenta? Iniciar Sesión</a>
        </div>

        <button type="submit" class="boton">Registrarse</button>
    </form>
</div>
<br>
<footer>
    © 2024 RH Innovate. Todos los derechos reservados.
</footer>
</body>
</html>
