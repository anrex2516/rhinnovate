<?php

session_start();


if (!isset($_SESSION['usuario_id'])) {
    
    header("Location: login.php");
    exit;
}else {
    session_destroy();
}

include 'db.php';


$correo_usuario = $_SESSION['correo'] ?? null;

if (isset($conn) && $conn->connect_error == null) {
    $sql = "SELECT * FROM ofertas_empleo";
    $result = $conn->query($sql);
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ofertas de Empleo</title>
    <link rel="stylesheet" href="ofertas.css">
    <style>
        .d-none { display: none; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; }
        .form-group { margin-bottom: 15px; }

        .profile-container {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
        }

        .profile-menu {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-menu a {
            padding: 10px 20px;
            display: block;
            text-decoration: none;
            color: #333;
        }

        .profile-menu a:hover {
            background-color: #f0f0f0;
        }
    </style>
    <script>
        function toggleMenu() {
            var menu = document.getElementById('profile-menu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        function aplicarOferta(idOferta, yaPostulado) {
            if (yaPostulado) {
                alert("Ya te has postulado a esta oferta.");
            } else {
                window.location.href = "formulario_postulacion.php?id_oferta=" + idOferta;
            }
        }
    </script>
</head>
<body>

    <div class="profile-container" onclick="toggleMenu()">
        <div class="profile-icon">👤</div>
        <div id="profile-menu" class="profile-menu">
            <a href="editar_perfil.php">Editar perfil</a>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>

    <h1>Ofertas de Empleo Destacadas</h1>
    <p class="sub">Encuentra tu próxima oportunidad profesional</p>

    <div class="caj">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="ofert">
                    <h2><?php echo htmlspecialchars($row['titulo']); ?></h2>
                    <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                    <p><strong>Requisitos:</strong> <?php echo htmlspecialchars($row['requisitos']); ?></p>
                    <p><em>Publicado el: <?php echo htmlspecialchars($row['fecha_publicacion']); ?></em></p>

                    <?php
                    $ya_postulado = false;

                    if ($correo_usuario) {
                        $stmtCheck = $conn->prepare("SELECT id_postulacion FROM postulaciones WHERE id_oferta = ? AND correo = ?");
                        $stmtCheck->bind_param("is", $row['id_oferta'], $correo_usuario);
                        $stmtCheck->execute();
                        $stmtCheck->store_result();
                        $ya_postulado = $stmtCheck->num_rows > 0;
                        $stmtCheck->close();
                    }
                    ?>

                    <button type="button" onclick="aplicarOferta(<?php echo $row['id_oferta']; ?>, <?php echo $ya_postulado ? 'true' : 'false'; ?>)">
                        Aplicar
                    </button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay ofertas de empleo disponibles en este momento.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($conn) && $conn->connect_error == null): ?>
        <?php $conn->close(); ?>
    <?php endif; ?>
</body>
</html>
