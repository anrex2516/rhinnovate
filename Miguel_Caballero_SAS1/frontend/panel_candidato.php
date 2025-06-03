<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include_once __DIR__ . '/../backend/db/db.php';

$usuario_id = $_SESSION['usuario_id'];

// Obtener postulaciones del usuario con detalle de la oferta
$sql = "
    SELECT p.*, o.titulo, o.ubicacion, o.departamento, o.salario, o.fecha_publicacion, o.descripcion, o.requisitos
    FROM postulaciones p
    JOIN ofertas_empleo o ON p.id_oferta = o.id_oferta
    WHERE p.id_usuario = ?
    ORDER BY p.fecha_postulacion DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$postulaciones = [];
while ($row = $result->fetch_assoc()) {
    $postulaciones[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Postulaciones</title>
    <link rel="stylesheet" href="css/postulaciones.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 230px;
            background-color: #1e2a38;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 10px;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #31465b;
        }

        .main {
            flex-grow: 1;
            padding: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .card h3 {
            margin-top: 0;
        }

        .info-line {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge.pendiente { background-color: #fdf4d6; color: #b58900; }
        .badge.proceso { background-color: #e0f0ff; color: #0074cc; }
        .badge.rechazado { background-color: #fce0e0; color: #cc0000; }
        .badge.contratado { background-color: #d6f4d6; color: #168d00; }

        .comentario {
            background-color: #edf4ff;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            color: #003366;
            font-size: 14px;
        }

        .ver-detalles {
            margin-top: 15px;
            display: inline-block;
            padding: 7px 14px;
            background-color: #111;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
        }

        .ver-detalles:hover {
            background-color: #444;
        }

        h1 {
            margin-bottom: 10px;
        }

        .sub {
            color: #666;
            font-size: 15px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Panel Candidato</h2>
        <strong style="margin-bottom: 10px; padding-bottom: 10px;"><?php echo $_SESSION['usuario_nombre']; ?></strong>
        <br>
        <a href="panel_candidato.php" class="active"> Mis Postulaciones</a>
        <a href="editar_perfil.php">Editar Perfil</a>
        <a href="ofertas.php">Volver a las ofertas</a>
        <a href="logout.php"> Cerrar Sesión</a>
    </div>

    <div class="main">
        <h1>Mis Postulaciones</h1>
        <p class="sub">Revisa el estado de tus aplicaciones laborales</p>

        <?php foreach ($postulaciones as $p): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($p['titulo']); ?></h3>
                <div class="info-line">
                    🏢 Miguel Caballero SAS · 📍 <?php echo htmlspecialchars($p['departamento']); ?> · 📅 <?php echo date('d M Y', strtotime($p['fecha_postulacion'])); ?>
                </div>
                <div class="info-line">
                    💰 Salario: <?php echo htmlspecialchars($p['salario']); ?>
                </div>
                <span class="badge 
                    <?php 
                        echo $p['estado'] === 'pendiente' ? 'pendiente' : 
                             ($p['estado'] === 'En proceso' ? 'proceso' : 
                             ($p['estado'] === 'Contratado' ? 'contratado' : 'rechazado')); ?>">
                    <?php 
                        echo $p['estado'] === 'pendiente' ? 'Pendiente' : 
                             ($p['estado'] === 'En proceso' ? 'En proceso' : 
                             ($p['estado'] === 'Contratado' ? '¡Contratado!' : 'No seleccionado')); ?>
                </span>

                <?php if (!empty($p['comentario_supervisor'])): ?>
                    <div class="comentario">
                        <strong>Último comentario:</strong><br>
                        <?php echo htmlspecialchars($p['comentario_supervisor']); ?>
                    </div>
                <?php endif; ?>

                <a class="ver-detalles" href="detalle_postulacion.php?id=<?php echo $p['id_postulacion']; ?>"> Ver detalles</a>
            </div>
        <?php endforeach; ?>

        <?php if (empty($postulaciones)): ?>
            <p>No tienes postulaciones aún.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
