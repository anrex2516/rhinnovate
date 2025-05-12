<!-- <?php
include 'db.php';

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
    <style>
        .d-none { display: none; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; }
        .form-container { margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }

        /* Estilos para el ícono y menú de perfil */
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
    </script>
    <link rel="stylesheet" href="ofertas.css">
</head>
<body>
    
    <!-- Ícono de perfil y menú -->
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
                <button type="button" onclick="mostrarFormulario(<?php echo $row['id_oferta']; ?>)">Ver detalles y aplicar</button>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No hay ofertas de empleo disponibles en este momento.</p>
    <?php endif; ?>
    </div>

    <div id="formularioPostulacion" class="container d-none mt-5">
        <div class="form-container">
            <h2>Formulario de Postulación</h2>
            <form action="submit_postulacion.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="oferta_id" name="oferta_id">
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" required>
                </div>
                <div class="form-group">
                    <label for="cv">Currículum (PDF)</label>
                    <label class="upload-container">
                        <div class="upload-icon">⬆️</div>
                        <span class="upload-text">Sube un archivo</span> o arrastra y suelta  
                        <br>  
                        <small>PDF hasta 10MB</small>
                        <input type="file" id="cv" name="cv" accept=".pdf" required class="file-input" onchange="showFileName(event)">
                        <p class="file-info" id="file-info">Ningún archivo seleccionado</p>
                    </label>
                </div>
                <button type="submit">Enviar Postulación</button>
            </form>
            <div class="message">
                <?php
                if (isset($_GET['message'])) {
                    echo htmlspecialchars($_GET['message']);
                }
                ?>
            </div>
        </div>
    </div>

    <?php if (isset($conn) && $conn->connect_error == null): ?>
        <?php $conn->close(); ?>
    <?php endif; ?>

    <script>
        function showFileName(event) {
            const file = event.target.files[0];
            if (file) {
                document.getElementById('file-info').textContent = "Archivo seleccionado: " + file.name;
            } else {
                document.getElementById('file-info').textContent = "Ningún archivo seleccionado";
            }
        }
    </script>

</body>
</html> -->
