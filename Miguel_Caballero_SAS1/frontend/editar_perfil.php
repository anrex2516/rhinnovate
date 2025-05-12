<?php
session_start(); // Iniciar sesión

if (!isset($_SESSION['usuario_id'])) {
    // Redirigir al login si la sesióon no esta activa
    header("Location: login.php");
    exit();
}

include 'db.php'; 

$usuario_id = $_SESSION['usuario_id'];
$usuario = null;

// Obtener los datos del usuario autenticado 
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// editar los datos si se envia y se revisa la base de datos correctamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    $update_sql = "UPDATE usuarios SET nombre_completo = ?, correo = ?, telefono = ? WHERE id_usuario = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $nombre, $correo, $telefono, $usuario_id);

    if ($update_stmt->execute()) {
        $message = "Perfil actualizado con éxito.";
        // Refrescar los datos
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
    } else {
        $message = "Error al actualizar el perfil.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <style>
        .form-container { max-width: 600px; margin: auto; padding: 20px;  }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        .message { color: green; margin-top: 15px; }



        /**estilos para el icono del perfil  */
        .profile-container {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
        }

        .profile-menu {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-menu a {
            padding: 10px 20px;
            display: block;
            text-decoration: none;
            color: #333;
        }

        .profile-menu a:hover {
            background-color: #f0f0f0;
        }
    </style>
    <link rel="stylesheet" href="editar_perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<script>
            function toggleMenu() {
            var menu = document.getElementById('profile-menu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }
</script>


<div class="profile-container" onclick="toggleMenu()">
        <div class="profile-icon">👤</div>
        <div id="profile-menu" class="profile-menu">
        <a href="ofertas.php">Volver</a>
           
        </div>
    </div>


<?php if (isset($message)): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($usuario): ?>
    <div class="form-container">


    <form action="editar_perfil.php" method="POST" class="form-modern">
    <h2>Editar Perfil</h2>
    <p class="subtitle">Actualiza tu información personal</p>

    <div class="inputBox">
        <label for="nombre">Nombre Completo</label>
        <div class="input-wrapper">
            <i class="fas fa-user"></i>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre completo" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
        </div>
    </div>

    <div class="inputBox">
        <label for="correo">Correo Electrónico</label>
        <div class="input-wrapper">
            <i class="fas fa-envelope"></i>
            <input type="email" id="correo" name="correo" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
        </div>
    </div>

    <div class="inputBox">
        <label for="telefono">Teléfono</label>
        <div class="input-wrapper">
            <i class="fas fa-phone"></i>
            <input type="text" id="telefono" name="telefono" placeholder="Número de teléfono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
        </div>
    </div>

    <div class="buttons">
        <button type="button" class="cancelar" >Cancelar</button>
        <button type="submit" class="actualizar"><i class="fas fa-lock"></i> Actualizar Perfil</button>
    </div>
</form>

</div>

<?php else: ?>
    <p>No se encontró el usuario.</p>
<?php endif; ?>

</body>
</html>
