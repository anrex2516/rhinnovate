<?php
include_once __DIR__ . '/../backend/db/db.php';

// Obtener todas las ofertas activas con la cantidad de postulantes
$sql = "
    SELECT o.id_oferta, o.titulo, o.departamento, COUNT(p.id_postulacion) AS total_postulantes
    FROM ofertas_empleo o
    LEFT JOIN postulaciones p ON o.id_oferta = p.id_oferta
    WHERE o.estado = 'activa'
    GROUP BY o.id_oferta
";
$ofertas = $conn->query($sql);

// Actualizar estado del postulante si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cambiar_estado'])) {
    $id = intval($_POST['id_postulacion']);
    $estado = $_POST['estado'];
    $comentario = $_POST['comentario'] ?? '';

    $sqlUpdate = "UPDATE postulaciones SET estado = ?, comentario_supervisor = ? WHERE id_postulacion = ?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("ssi", $estado, $comentario, $id);
    $stmt->execute();
    $stmt->close();
}

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
    <style>
        .modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); display: flex; justify-content: center; align-items: center;
            display: none;
        }
        .modal-content {
            background: #fff; padding: 30px; border-radius: 8px; width: 400px; max-width: 90%;
        }
        .close { float: right; font-size: 24px; cursor: pointer; }
        .acciones button { margin: 0 2px; padding: 4px 10px; }
    </style>
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
            <label for="oferta">Seleccionar Oferta Laboral</label>
            <select name="id_oferta" onchange="this.form.submit()">
                <option value="">Seleccione una oferta laboral</option>
                <?php while($row = $ofertas->fetch_assoc()): ?>
                    <option value="<?php echo $row['id_oferta']; ?>" <?php if (isset($_GET['id_oferta']) && $_GET['id_oferta'] == $row['id_oferta']) echo "selected"; ?>>
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
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($postulante = $postulantes->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $postulante['nombre']; ?></td>
                                    <td><?php echo $postulante['correo']; ?></td>
                                    <td><?php echo $postulante['telefono']; ?></td>
                                    <td><?php echo $postulante['estado']; ?></td>
                                    <td class="acciones">
                                        <button onclick="abrirModal(<?php echo $postulante['id_postulacion']; ?>, 'En proceso')">🕒</button>
                                        <button onclick="abrirModal(<?php echo $postulante['id_postulacion']; ?>, 'Contratado')">✅</button>
                                        <button onclick="abrirModal(<?php echo $postulante['id_postulacion']; ?>, 'Rechazado')">❌</button>
                                    </td>
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

<!-- MODAL -->
<div id="estadoModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="cerrarModal()">&times;</span>
    <h2 id="modal-titulo">Cambiar Estado</h2>
    <form id="form-estado" method="POST">
      <input type="hidden" name="id_postulacion" id="modal-id">
      <input type="hidden" name="estado" id="modal-estado">
      <label for="comentario">Comentario para el postulante (opcional)</label>
      <textarea name="comentario" placeholder="Escribe un comentario..." rows="4"></textarea>
      <br><br>
      <button type="button" onclick="cerrarModal()">Cancelar</button>
      <button type="submit" name="cambiar_estado">Confirmar y enviar</button>
    </form>
  </div>
</div>

<script>
  function abrirModal(id, estado) {
    document.getElementById('estadoModal').style.display = 'flex';
    document.getElementById('modal-id').value = id;
    document.getElementById('modal-estado').value = estado;
    document.getElementById('modal-titulo').innerText = "Cambiar estado a: " + estado;
  }

  function cerrarModal() {
    document.getElementById('estadoModal').style.display = 'none';
  }
</script>
</body>
</html>
