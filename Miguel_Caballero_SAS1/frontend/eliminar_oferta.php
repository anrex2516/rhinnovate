<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_oferta'])) {
    $id = intval($_POST['id_oferta']);

    // Mirar si la oferta esta cerrada para poder eliminarla
    $verifica = $conn->query("SELECT estado FROM ofertas_empleo WHERE id_oferta = $id");
    $estado = $verifica->fetch_assoc()['estado'];

    if ($estado === 'cerrada') {
        // Eliminar las postulaciones que hayan para poder eliminar la oferta
        $conn->query("DELETE FROM postulaciones WHERE id_oferta = $id");

        // Eliminar oferta
        if ($conn->query("DELETE FROM ofertas_empleo WHERE id_oferta = $id")) {
            header("Location: ofertas_activas.php?msg=oferta_eliminada");
            exit;
        } else {
            echo "Error al eliminar la oferta: " . $conn->error;
        }
    } else {
        echo "Solo se pueden eliminar ofertas cerradas.";
    }
}
?>
