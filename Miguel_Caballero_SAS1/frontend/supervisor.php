<?php
session_start();

// Verifica si el usuario es supervisor
if ($_SESSION['role'] != 'supervisor') {
    header("Location: login.php"); // Si no es supervisor, redirige al login
    exit;
}

include_once __DIR__ . '/../backend/db/db.php';

// Si se asignó un horario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleado_id = $_POST['empleado_id'];
    $horario = $_POST['horario'];

    // Verifica si ya existe un horario para el empleado
    $sql_check = "SELECT * FROM horarios WHERE empleado_id = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("i", $empleado_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Si ya existe un horario, lo actualizamos
            $sql = "UPDATE horarios SET horario = ? WHERE empleado_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("si", $horario, $empleado_id);
                if ($stmt->execute()) {
                    echo "Horario actualizado correctamente.";
                } else {
                    echo "Error al actualizar el horario.";
                }
            }
        } else {
            // Si no existe un horario, insertamos uno nuevo
            $sql = "INSERT INTO horarios (empleado_id, horario) VALUES (?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("is", $empleado_id, $horario);
                if ($stmt->execute()) {
                    echo "Horario asignado correctamente.";
                } else {
                    echo "Error al asignar el horario.";
                }
            }
        }
    }

    $conn->close();
}

// Obtener lista de empleados con rol 'employee'
$sql = "SELECT * FROM usuarios WHERE rol = 'employee'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Supervisor</title>
    <link rel="stylesheet" href="supervisor.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="../index.html">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Asignar Horarios a los Empleados</h2>
        <form method="POST" action="supervisor.php">
            <label for="empleado">Empleado:</label>
            <select name="empleado_id" required>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id_usuario']; ?>"><?php echo $row['nombre_completo']; ?></option>
                <?php } ?>
            </select>

            <label for="horario">Horario:</label>
            <input type="text" name="horario" placeholder="Ejemplo: 9:00 - 17:00" required>

            <button type="submit">Asignar Horario</button>
        </form>
    </div>
</body>
</html>
