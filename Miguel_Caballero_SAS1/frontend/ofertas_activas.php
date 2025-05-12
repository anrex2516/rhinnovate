<?php
include 'db.php';

$departamento = $_GET['departamento'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$orden = $_GET['orden'] ?? 'recientes';

// Consulta SQL base
$sql = "SELECT * FROM ofertas_empleo WHERE 1";

// Filtrar por departamento
if (!empty($departamento)) {
    $sql .= " AND departamento = ?";
}

// Filtrar por búsqueda en el título
if (!empty($busqueda)) {
    $sql .= " AND titulo LIKE ?";
}

// Ordenar
$orderClause = $orden === 'antiguos' ? 'ASC' : 'DESC';
$sql .= " ORDER BY fecha_publicacion $orderClause";

// Preparar consulta
$stmt = $conn->prepare($sql);

// Bind dinámico
$params = [];
$types = '';
if (!empty($departamento)) {
    $types .= 's';
    $params[] = &$departamento;
}
if (!empty($busqueda)) {
    $busquedaLike = "%$busqueda%";
    $types .= 's';
    $params[] = &$busquedaLike;
}

if (!empty($params)) {
    array_unshift($params, $types);
    call_user_func_array([$stmt, 'bind_param'], $params);
}

$stmt->execute();
$result = $stmt->get_result();

// Departamentos de Colombia
$departamentos = [
    "Amazonas", "Antioquia", "Arauca", "Atlántico", "Bolívar", "Boyacá", "Caldas",
    "Caquetá", "Casanare", "Cauca", "Cesar", "Chocó", "Córdoba", "Cundinamarca",
    "Guainía", "Guaviare", "Huila", "La Guajira", "Magdalena", "Meta", "Nariño",
    "Norte de Santander", "Putumayo", "Quindío", "Risaralda", "San Andrés y Providencia",
    "Santander", "Sucre", "Tolima", "Valle del Cauca", "Vaupés", "Vichada"
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ofertas Activas</title>
    <link rel="stylesheet" href="ofertas_admin.css">
    <style>
        .action-btn {
            background: #1a1a1a;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: red;
            min-width: 150px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
            z-index: 1;
            padding: 10px 0;
            border-radius: 6px;
        }

        .dropdown-content form {
            margin: 0;
            padding: 0;
        }

        .dropdown-content button {
            width: 100%;
            padding: 8px 12px;
            background: none;
            border: none;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-content button:hover {
            background-color: green;
        }

        select, input[type="text"] {
            padding: 5px;
            border-radius: 4px;
            margin-right: 5px;
        }

    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>RH Dashboard</h2>
        <a href="admin_ofertas.php">Crear Oferta Laboral</a>
        <a href="#" class="active">Ofertas Activas</a>
        <a href="postulantes_por_oferta.php">Postulantes por Oferta</a>
        <a href="editar_perfil_supervisor.php">Editar perfil</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <div class="main">
        <h1>Ofertas Laborales Activas</h1>
        <p class="sub">Gestione y supervise todas las ofertas de trabajo actualmente publicadas.</p>

        <div class="card">
            <div class="card-header">
                <div class="left">
                    <h2>Ofertas Activas</h2>
                    <p>Actualmente hay <?php echo $result->num_rows; ?> ofertas laborales activas.</p>
                </div>
                <div class="right">
          
                    <form method="GET" action="">
                        <input type="text" name="busqueda" placeholder="Buscar ofertas..." value="<?php echo htmlspecialchars($busqueda); ?>">

                        <select name="departamento" onchange="this.form.submit()">
                            <option value="">Todos los departamentos</option>
                            <?php foreach ($departamentos as $dep): ?>
                                <option value="<?php echo $dep; ?>" <?php echo ($departamento === $dep) ? 'selected' : ''; ?>>
                                    <?php echo $dep; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="orden" onchange="this.form.submit()">
                            <option value="recientes" <?php if ($orden === 'recientes') echo 'selected'; ?>>Más recientes primero</option>
                            <option value="antiguos" <?php if ($orden === 'antiguos') echo 'selected'; ?>>Más antiguos primero</option>
                        </select>

                        <button type="submit" class="ftl" style="color:white;   background-color: #1e293b;">Filtrar</button>
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Puesto</th>
                        <th>Departamento</th>
                        <th>Ubicación</th>
                        <th>Postulantes</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo $row['titulo']; ?></strong><br>
                                <?php if ($row['trabajo_remoto']): ?><span class="badge badge-blue">Remoto</span><?php endif; ?>
                                <?php if ($row['contratacion_urgente']): ?><span class="badge badge-red">Urgente</span><?php endif; ?>
                            </td>
                            <td><?php echo $row['departamento']; ?></td>
                            <td><?php echo $row['ubicacion']; ?></td>
                            <td>
                                <?php
                                    $id = $row['id_oferta'];
                                    $queryPost = $conn->query("SELECT COUNT(*) as total FROM postulaciones WHERE id_oferta = $id");
                                    $count = $queryPost->fetch_assoc()['total'];
                                    echo $count;
                                ?>
                            </td>
                            <td><?php echo $row['fecha_publicacion']; ?></td>
                            <td>
                                <div class="dropdown">
                                    <button onclick="toggleDropdown(this)" class="action-btn">⋮</button>
                                    <div class="dropdown-content">
                                        <form method="POST" action="eliminar_oferta.php" onsubmit="return confirm('¿Seguro que deseas eliminar esta oferta?')">
                                            <input type="hidden" name="id_oferta" value="<?php echo $row['id_oferta']; ?>">
                                            <button type="submit">Eliminar</button>
                                        </form>
                                        <form method="POST" action="cerrar_oferta.php">
                                            <input type="hidden" name="id_oferta" value="<?php echo $row['id_oferta']; ?>">
                                            <button type="submit">Cerrar</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    const all = document.querySelectorAll('.dropdown-content');
    all.forEach(menu => {
        if (menu !== dropdown) menu.style.display = 'none';
    });
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

window.onclick = function(e) {
    if (!e.target.matches('.action-btn')) {
        document.querySelectorAll('.dropdown-content').forEach(menu => {
            menu.style.display = 'none';
        });
    }
}
</script>
</body>
</html>
