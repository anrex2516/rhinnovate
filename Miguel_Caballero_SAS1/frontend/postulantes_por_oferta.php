<?php
include 'db.php';

// Obtener todas las ofertas activas con la canitidad de postulantes
$sql = "
    SELECT o.id_oferta, o.titulo, o.departamento, COUNT(p.id_postulacion) AS total_postulantes
    FROM ofertas_empleo o
    LEFT JOIN postulaciones p ON o.id_oferta = p.id_oferta
    WHERE o.estado = 'activa'
    GROUP BY o.id_oferta
";
$ofertas = $conn->query($sql);

// Si se seleccionó una oferta específica
$postulantes = [];
if (isset($_GET['id_oferta'])) {
    $id_oferta = intval($_GET['id_oferta']);

    $query = "SELECT * FROM postulaciones WHERE id_oferta = $id_oferta";
    $postulantes = $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Postulantes por Oferta</title>
    <link rel="stylesheet" href="ofertas_admin.css">
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>RH Dashboard</h2>
        <a href="admin_ofertas.php">Crear Oferta Laboral</a>
        <a href="ofertas_activas.php">Ofertas Activas</a>
        <a href="#" class="active">Postulantes por Oferta</a>
        <a href="editar_perfil_supervisor.php">Editar perfil</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <div class="main">
        <h1>Postulantes por Oferta</h1>
        <p class="sub">Revise y gestione los candidatos para cada oferta laboral.</p>

        <form method="GET" class="card">
            <label for="oferta" >Seleccionar Oferta Laboral</label>
            <select name="id_oferta" onchange="this.form.submit()" style="padding: 10px; border-radius:5px;">
                <option value="" style="margin-top:20px; border-bottom:1px solid gray;">Seleccione una oferta laboral</option>
                <?php while($row = $ofertas->fetch_assoc()): ?>
                    <option value="<?php echo $row['id_oferta']; ?>" 
                        <?php if (isset($_GET['id_oferta']) && $_GET['id_oferta'] == $row['id_oferta']) echo "selected"; ?>>
                        <?php echo $row['titulo'] . " - " . $row['departamento'] . " (" . $row['total_postulantes'] . " postulantes)"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <?php if (isset($_GET['id_oferta'])): ?>
            <div class="card">
                <h2>Lista de Postulantes</h2>
                <?php if ($postulantes->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Hoja de Vida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($postulante = $postulantes->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $postulante['nombre']; ?></td>
                                    <td><?php echo $postulante['correo']; ?></td>
                                    <td><?php echo $postulante['telefono']; ?></td>
                                    <td><a href="<?php echo $postulante['cv']; ?>" target="_blank">Ver</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay postulantes para esta oferta.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
