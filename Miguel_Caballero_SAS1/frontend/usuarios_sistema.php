<?php
include 'db.php';

$rolFiltro = $_GET['rol'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

$sql = "SELECT nombre_completo, correo, rol, telefono FROM usuarios WHERE 1";

if (!empty($rolFiltro)) {
    $sql .= " AND rol = ?";
}
if (!empty($busqueda)) {
    $sql .= " AND (nombre_completo LIKE ? OR correo LIKE ?)";
}
$sql .= " ORDER BY nombre_completo ASC";

$stmt = $conn->prepare($sql);

$params = [];
$types = '';
if (!empty($rolFiltro)) {
    $types .= 's';
    $params[] = &$rolFiltro;
}
if (!empty($busqueda)) {
    $busquedaLike = "%$busqueda%";
    $types .= 'ss';
    $params[] = &$busquedaLike;
    $params[] = &$busquedaLike;
}

if (!empty($params)) {
    array_unshift($params, $types);
    call_user_func_array([$stmt, 'bind_param'], $params);
}

$stmt->execute();
$result = $stmt->get_result();

$roles = ['admin', 'usuario', 'gerente', 'supervisor'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Registrados</title>
    <link rel="stylesheet" href="ofertas_admin.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background-color: #1e293b;
            color: white;
        }

        button {
            padding: 6px 12px;
            margin-left:30px;
            background-color:rgb(133, 13, 13);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #b91c1c;
        }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>RH Dashboard</h2>
        <a href="admin.php">Actualizar Datos</a>
        <a href="#" class="active">Usuarios</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <div class="main">
        <h1>Usuarios Registrados</h1>
        <p class="sub">Consulta los usuarios de la plataforma por rol y nombre.</p>

        <form method="GET" action="">
            <input type="text" name="busqueda" placeholder="Buscar por nombre o correo" value="<?php echo htmlspecialchars($busqueda); ?>">
            <select name="rol" onchange="this.form.submit()">
                <option value="">Todos los roles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?php echo $r; ?>" <?php echo ($rolFiltro === $r) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($r); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filtrar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th style="color:white;">Nombre Completo</th>
                    <th style="color:white;">Correo</th>
                    <th style="color:white;">Rol</th>
                    <th style="color:white;">Teléfono</th>
                    <th style="color:white;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr id="fila-<?php echo md5($row['correo']); ?>">
                            <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($row['correo']); ?></td>
                            <td><?php echo htmlspecialchars($row['rol']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                            <td>
                                <button onclick="eliminarUsuario('<?php echo htmlspecialchars($row['correo']); ?>', 'fila-<?php echo md5($row['correo']); ?>')">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No se encontraron usuarios.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function eliminarUsuario(correo, filaId) {
    if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
        fetch('eliminar_usuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'correo=' + encodeURIComponent(correo)
        })
        .then(response => response.text())
        .then(data => {
            alert("Usuario eliminado con éxito");
            const fila = document.getElementById(filaId);
            if (fila) fila.remove();
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Ocurrió un error al eliminar el usuario");
        });
    }
}
</script>

</body>
</html>
