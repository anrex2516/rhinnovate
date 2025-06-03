<?php
include_once __DIR__ . '/../backend/db/db.php';
session_start(); // Para mantener sesión activa

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_POST['oferta_id'], $_POST['nombre'], $_POST['correo'], $_POST['telefono'], $_FILES['cv'])) {
        $_SESSION['error'] = "Faltan datos para la postulación.";
        header("Location: formulario_postulacion.php?oferta_id=" . urlencode($_POST['oferta_id']));
        exit();
    }

    $id_usuario = $_SESSION['usuario_id'] ?? null; // Asegura que la sesión esté activa
    if (!$id_usuario) {
        $_SESSION['error'] = "Debes iniciar sesión para postularte.";
        header("Location: login.php");
        exit();
    }

    $oferta_id = $_POST['oferta_id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $cv = $_FILES['cv'];

    // Validar postulación previa
    $checkQuery = "SELECT id_postulacion FROM postulaciones WHERE id_usuario = ? AND id_oferta = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $id_usuario, $oferta_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $_SESSION['error'] = "❌ Ya te has postulado a esta oferta.";
        header("Location: formulario_postulacion.php?oferta_id=" . urlencode($oferta_id));
        exit();
    }

    // Subir archivo CV
    $cv_path = 'uploads/' . basename($cv['name']);
    if (move_uploaded_file($cv['tmp_name'], $cv_path)) {
        $sql = "INSERT INTO postulaciones (id_usuario, id_oferta, nombre, correo, telefono, cv, estado)
                VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iissss", $id_usuario, $oferta_id, $nombre, $correo, $telefono, $cv_path);
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
    header("Location: formulario_postulacion.php?oferta_id=" . urlencode($oferta_id));
    exit();
}
?>
