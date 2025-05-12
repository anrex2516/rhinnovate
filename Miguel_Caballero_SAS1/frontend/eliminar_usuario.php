<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['correo'])) {
    $correo = $_POST['correo'];

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);

    if ($stmt->execute()) {
        header("Location: ver_usuarios.php?mensaje=usuario_eliminado");
        exit();
    } else {
        echo "Error al eliminar usuario.";
    }
} else {
    echo "Solicitud no válida.";
}
?>
