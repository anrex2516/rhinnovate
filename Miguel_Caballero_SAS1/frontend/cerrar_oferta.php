<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_oferta'])) {
    $id = intval($_POST['id_oferta']);
    $sql = "UPDATE ofertas_empleo SET estado = 'cerrada' WHERE id_oferta = $id";
    if ($conn->query($sql)) {
        header("Location: ofertas_activas.php?msg=oferta_cerrada");
        exit;
    } else {
        echo "Error al cerrar la oferta: " . $conn->error;
    }
}
?>
