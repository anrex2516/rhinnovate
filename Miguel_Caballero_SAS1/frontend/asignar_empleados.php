<?php
session_start();
include 'db.php';

// Verificar si el usuario tiene rol de supervisor
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'supervisor') {
    header("Location: login.php"); // Redirige al login si no es supervisor
    exit;
}

// Obtener todos los usuarios con rol 'employee'
$sql = "SELECT id_usuario, nombre_completo, correo FROM usuarios WHERE rol = 'employee'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Cargo y Departamento</title>
</head>
<body>
    <h2>Asignar Cargo y Departamento a Empleado</h2>

    <form action="asignar_empleado.php" method="POST">
        <label for="empleado">Seleccionar Empleado:</label>
        <select name="empleado_id" id="empleado">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id_usuario'] . "'>" . $row['nombre_completo'] . " (" . $row['correo'] . ")</option>";
                }
            } else {
                echo "<option value=''>No hay empleados disponibles</option>";
            }
            ?>
        </select><br><br>

        <label for="cargo">Cargo:</label>
        <input type="text" name="cargo" required><br><br>

        <label for="departamento">Departamento:</label>
        <input type="text" name="departamento" required><br><br>

        <button type="submit">Asignar</button>
    </form>

</body>
</html>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleado_id = $_POST['empleado_id'];
    $cargo = $_POST['cargo'];
    $departamento = $_POST['departamento'];


    if (!empty($empleado_id) && !empty($cargo) && !empty($departamento)) {

        $sql_insert = "INSERT INTO empleados (id_usuario, cargo, departamento) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql_insert)) {
        
            $stmt->bind_param("iss", $empleado_id, $cargo, $departamento);
        
            if ($stmt->execute()) {
                echo "Empleado asignado correctamente.";
            } else {
                echo "Error al asignar empleado: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }
    } else {
        echo "Por favor, complete todos los campos del formulario.";
    }
}

$conn->close();
?>
