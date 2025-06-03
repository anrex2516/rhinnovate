<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    
    header("Location: login.php");
    exit;
}
include_once __DIR__ . '/../backend/db/db.php';


$user_info = null;
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search_email'])) {
        $search_email = $_POST['search_email'];
        $sql = "SELECT nombre_completo, correo, rol FROM usuarios WHERE correo = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $search_email);
            $stmt->execute();
            $stmt->bind_result($nombre_completo, $correo, $rol);
            if ($stmt->fetch()) {
                $user_info = [
                    'nombre_completo' => $nombre_completo,
                    'correo' => $correo,
                    'rol' => $rol
                ];
            } else {
                $message = "No se encontró un usuario con ese correo.";
            }
            $stmt->close();
        } else {
            $message = "Error en la consulta: " . $conn->error;
        }
    } elseif (isset($_POST['email']) && isset($_POST['role'])) {

        $email = $_POST['email'];
        $role = $_POST['role'];

        $sql = "UPDATE usuarios SET rol = ? WHERE correo = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $role, $email);
            $stmt->execute();
            $stmt->close();
            $message = "Rol actualizado correctamente.";
        } else {
            $message = "Error en la actualización del rol: " . $conn->error;
        }
    } elseif (isset($_POST['update_email']) && isset($_POST['update_name']) && isset($_POST['original_email'])) {
        $update_email = $_POST['update_email'];
        $update_name = $_POST['update_name'];
        $original_email = $_POST['original_email'];

        $sql = "UPDATE usuarios SET correo = ?, nombre_completo = ? WHERE correo = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $update_email, $update_name, $original_email);
            $stmt->execute();
            $stmt->close();
            $message = "Datos del usuario actualizados correctamente.";
        } else {
            $message = "Error en la actualización de los datos: " . $conn->error;
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="stylesheet" href="styles.css">

    <style>

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #f8f9fa;
}

.dashboard {
    display: flex;
    height: 100vh;
}

.sidebar {
    background-color: #1f1f2e;
    padding: 20px;
    width: 240px;
    color: white;
}

.sidebar h2 {
    font-size: 20px;
    color:white;
    margin-bottom: 30px;
}

.sidebar a {
    display: block;
    padding: 10px;
    margin-bottom: 10px;
    color: #c2c2c2;
    text-decoration: none;
    border-radius: 5px;
    transition: 0.3s;
}

.sidebar a:hover,
.sidebar a.active {
    background-color: #35354a;
    color: #fff;
}

.admin-container{
    display:flex;
    width: 1200px;
    justify-content: center;
    align-items: center;
    flex-direction: column
}
.user-rol{
    width: 350px;
    margin-top: 20px;
    padding: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.user-act{
    width: 350px;
    margin-top: 20px;
    padding: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 30px;
}
button {
    background-color:rgb(27, 27, 66);
    color: #fff;
    border: none;
    cursor: pointer;
}

    </style>
</head>
<body>


<div class="dashboard">
    <div class="sidebar">
        <h2>RH Dashboard Admin</h2>
        <a href="#" class="active">Actualizar Datos</a>
        <a href="usuarios_sistema.php">Usuarios</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
    <div class="admin-container">
        <h2>Bienvenido, Administrador</h2>
        <div class="message"><?php echo $message; ?></div>
        <form action="admin.php" method="post" class="search-form">
            <input type="email" name="search_email" placeholder="Buscar por correo" required>
            <button type="submit">Buscar</button>
            
    
        </form>


        <?php if ($user_info): ?>
            <div class="user-info">
                <h3>Información del Usuario</h3>
                <p>Nombre Completo: <?php echo htmlspecialchars($user_info['nombre_completo']); ?></p>
                <p>Correo: <?php echo htmlspecialchars($user_info['correo']); ?></p>
                <p>Rol: <?php echo htmlspecialchars($user_info['rol']); ?></p>
            </div>
            <div class="user-rol">
    
            <form action="admin.php" method="post" class="update-form">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($user_info['correo']); ?>">
                <select name="role">
                    <option value="user" <?php if ($user_info['rol'] == 'user') echo 'selected'; ?>>Usuario</option>
                    <option value="admin" <?php if ($user_info['rol'] == 'admin') echo 'selected'; ?>>Administrador</option>
                    <option value="supervisor" <?php if ($user_info['rol'] == 'supervisor') echo 'selected'; ?>>Supervisor</option>
                    
                </select>
                <button type="submit">Actualizar Rol</button>
            </form>
        </div>
        <div class="user-act">
            <form action="admin.php" method="post" class="update-form">
                <input type="hidden" name="original_email" value="<?php echo htmlspecialchars($user_info['correo']); ?>">
                <input type="text" name="update_name" value="<?php echo htmlspecialchars($user_info['nombre_completo']); ?>" placeholder="Nuevo nombre completo" required>
                <input type="email" name="update_email" value="<?php echo htmlspecialchars($user_info['correo']); ?>" placeholder="Nuevo correo" required>
                <button type="submit">Actualizar Datos</button>
                <br>
                
            </form>

        </div>
        <?php endif; ?>
    </div>
</body>
</html>
