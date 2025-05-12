<?php
session_start();
include 'db.php';

$oferta_id = $_GET['id_oferta'] ?? null;

if (!$oferta_id) {
    die("ID de oferta no especificado.");
}

// Verifica si la oferta existe
$stmt = $conn->prepare("SELECT * FROM ofertas_empleo WHERE id_oferta = ?");
$stmt->bind_param("i", $oferta_id);
$stmt->execute();
$result = $stmt->get_result();
$oferta = $result->fetch_assoc();

if (!$oferta) {
    die("Oferta no encontrada.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Postular a: <?php echo htmlspecialchars($oferta['titulo']); ?></title>
    <link rel="stylesheet" href="fp.css">
</head>
<body>

<!-- Mostrar mensajes de éxito o error -->
<?php
if (isset($_SESSION['error'])) {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; margin-bottom: 15px;'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; margin-bottom: 15px;'>" . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']);
}
?>

<h1>Postulación: <?php echo htmlspecialchars($oferta['titulo']); ?></h1>
<p><strong>Descripción del puesto:</strong> <?php echo htmlspecialchars($oferta['descripcion']); ?></p>

<div id="formularioPostulacion" class="container mt-5">
    <form action="submit_postulacion.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="oferta_id" value="<?php echo htmlspecialchars($oferta['id_oferta']); ?>">

        <label for="nombre">Nombre Completo</label>
        <input type="text" name="nombre" required><br>

        <label for="correo">Correo Electrónico</label>
        <input type="email" name="correo" required><br>

        <label for="telefono">Teléfono</label>
        <input type="text" name="telefono" required><br>

        <label for="cv">Currículum (PDF)</label>
        <input type="file" name="cv" accept=".pdf" required><br>

        <button type="submit">Enviar Postulación</button>
    </form>
</div>

</body>
</html>
