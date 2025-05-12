<?php
session_start(); //  iniciar la sesión
include 'db.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['username'] ?? '';
    $contrasena = $_POST['password'] ?? '';

    if (!empty($usuario) && !empty($contrasena)) {
        $sql = "SELECT * FROM usuarios WHERE correo = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if (password_verify($contrasena, $row['contrasena'])) {
                    $_SESSION['usuario_id'] = $row['id_usuario']; 
                    $_SESSION['usuario_nombre'] = $row['nombre_completo'];
                    $_SESSION['usuario_email'] = $row['correo'];
                    $_SESSION['usuario_rol'] = $row['rol'];
                
                    // Redirección según el rol
                    switch ($row['rol']) {
                        case 'admin':
                            header("Location: admin.php");
                            break;
                        case 'employee':
                            header("Location: empleados.php");
                            break;
                        case 'gerente':
                            header("Location: admin_ofertas.php");
                            break;
                        case 'supervisor':
                            header("Location: supervisor.php");
                            break;
                        default:
                            header("Location: ofertas.php");
                            break;
                    }
                    exit;
                } else {
                    echo "Contraseña incorrecta.";
                }
            } else {
                echo "No se encontró una cuenta con ese correo.";
            }

            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Por favor, complete todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="css/loginestilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="rhlogo.jpg" type="image/x-icon">
</head>
<body>
<div class="box">
    <form action="login.php" method="post">
        <h2>𖡌INICIAR SESIÓN𖡌</h2>
        <Label class="Rh">Bienvenido a RH Innovate</Label>

        <div class="inputBox">
            <i class="fas fa-user"></i>
            <input type="text" name="username" required>
            <label>Usuario:</label>
        </div>

        <div class="inputBox">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" required>
            <label>Contraseña:</label>
        </div>

        <div class="links">
            <a href="register.php">Registrarse</a>
        </div>
        <button type="submit" class="boton">Login</button>
    </form>
</div>

<footer>
    © 2024 RH Innovate. Todos los derechos reservados.
</footer>
</body>
</html>

