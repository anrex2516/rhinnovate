<?php
include_once __DIR__ . '/../backend/db/db.php';





if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['crear'])) {
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $requisitos = $_POST['requisitos'];
        $ubicacion = $_POST['ubicacion'];
        $salario = $_POST['salario'];
        $departamento = $_POST['departamento'];
        $tipo_contrato = $_POST['tipo_contrato'];
        $trabajo_remoto = isset($_POST['trabajo_remoto']) ? 1 : 0;
        $contratacion_urgente = isset($_POST['contratacion_urgente']) ? 1 : 0;

        $sql = "INSERT INTO ofertas_empleo (titulo, descripcion, requisitos, ubicacion, salario, departamento, tipo_contrato, trabajo_remoto, contratacion_urgente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssssii", $titulo, $descripcion, $requisitos, $ubicacion, $salario, $departamento, $tipo_contrato, $trabajo_remoto, $contratacion_urgente);
            if ($stmt->execute()) {
                $message = "Oferta de empleo creada correctamente.";
            } else {
                $message = "Error al crear la oferta de empleo: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error en la consulta: " . $conn->error;
        }
    } elseif (isset($_POST['eliminar'])) {
        $id_oferta = $_POST['id_oferta'];

        $sql = "DELETE FROM ofertas_empleo WHERE id_oferta = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $id_oferta);
            if ($stmt->execute()) {
                $message = "Oferta de empleo eliminada correctamente.";
            } else {
                $message = "Error al eliminar la oferta de empleo: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error en la consulta: " . $conn->error;
        }
    }
}

$sql = "SELECT * FROM ofertas_empleo";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>RH Dashboard</title>
    <link rel="stylesheet" href="ofertas_admin.css">
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>RH Dashboard</h2>
        <a href="#" class="active">Crear Oferta Laboral</a>
        <a href="ofertas_activas.php">Ofertas Activas</a>
        <a href="postulantes_por_oferta.php">Postulantes por Oferta</a>
        <a href="editar_perfil_supervisor.php">Editar perfil</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

<!-- Esto de abajo lo ajustaremos para una parte que va estar arriba como un tipo perfil que quiero que diga el nombre del supervisor que tiene iniciada la sesión  -->
    <!-- <h1  style="color:red; margin-left:400px;"><?php echo "Sesión activa como: " . $_SESSION['usuario_nombre'] . " (Rol: " . $_SESSION['usuario_rol'] . ")";  ?></h1> -->

    <div class="main">
        <h1>Crear Nueva Oferta Laboral</h1>
        <p>Complete el formulario para publicar una nueva oferta de trabajo.</p>

        <form method="post" action="admin_ofertas.php" class="formulario-oferta">
            <h2>Detalles de la Oferta</h2>

            <div class="form-grid">
                <div>
                    <label for="titulo">Título del Puesto</label>
                    <input type="text" name="titulo" id="titulo" placeholder="Ej: Desarrollador Frontend Senior" required>

                    <label for="ubicacion">Ubicación</label>
                    <input type="text" name="ubicacion" id="ubicacion" placeholder="Ej: Bogota" required>

                    <label for="salario">Salario (Opcional)</label>
                    <input type="text" name="salario" id="salario" placeholder="Ej: $1,430,000">
                </div>

                <div>
                    <label for="departamento">Departamento</label>
                    <input type="text" name="departamento" id="departamento" required>

                    <label for="tipo_contrato">Tipo de Contrato</label>
                    <input type="text" name="tipo_contrato" id="tipo_contrato" required>

                    <div class="checkboxes">
                        <label><input type="checkbox" name="trabajo_remoto"> Trabajo Remoto</label>
                        <label><input type="checkbox" name="contratacion_urgente"> Contratación Urgente</label>
                    </div>
                </div>
            </div>

            <label for="descripcion">Descripción del Puesto</label>
            <textarea name="descripcion" id="descripcion" placeholder="Describa las responsabilidades y objetivos del puesto..." required></textarea>

            <label for="requisitos">Requisitos</label>
            <textarea name="requisitos" id="requisitos" placeholder="Detalle los requisitos, habilidades y experiencia necesaria..." required></textarea>

            <button type="submit" name="crear">Crear Oferta Laboral</button>
        </form>

        <?php if (isset($message)): ?>
            <p class="mensaje"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</div>
</body>

</html>
