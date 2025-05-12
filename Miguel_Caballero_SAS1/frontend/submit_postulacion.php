<?php
include 'db.php';

session_start(); // Por si necesitas guardar mensajes en sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validar campos
    if (!isset($_POST['oferta_id'], $_POST['nombre'], $_POST['correo'], $_POST['telefono'], $_FILES['cv'])) {
        $_SESSION['error'] = "Faltan datos para la postulación.";
        header("Location: formulario_postulacion.php?oferta_id=" . urlencode($_POST['oferta_id']));
        exit();
    }

    $oferta_id = $_POST['oferta_id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $cv = $_FILES['cv'];

    // Verificar postulación previa
    $checkQuery = "SELECT id_postulacion FROM postulaciones WHERE correo = ? AND id_oferta = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("si", $correo, $oferta_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $_SESSION['error'] = "❌ Ya te has postulado a esta oferta. Solo puedes postularte una vez.";
        header("Location: formulario_postulacion.php?oferta_id=" . urlencode($oferta_id));
        exit();
    }

    // Subida del CV
    $cv_path = 'uploads/' . basename($cv['name']);
    if (move_uploaded_file($cv['tmp_name'], $cv_path)) {
        $sql = "INSERT INTO postulaciones (id_oferta, nombre, correo, telefono, cv, fecha_postulacion) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("issss", $oferta_id, $nombre, $correo, $telefono, $cv_path);
            if ($stmt->execute()) {
                $_SESSION['success'] = "✅ Tu postulación ha sido enviada correctamente.";
            } else {
                $_SESSION['error'] = "Error al guardar la postulación: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error al preparar la consulta: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Error al subir el archivo CV.";
    }

    $conn->close();

    // Redirige de vuelta al formulario (con el mismo ID)
    header("Location: formulario_postulacion.php?oferta_id=" . urlencode($oferta_id));
    exit();
}
?>
