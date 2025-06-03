<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include_once __DIR__ . '/../backend/db/db.php';
$correo_usuario = $_SESSION['usuario_email'] ?? null;

$sql = "SELECT * FROM ofertas_empleo WHERE estado = 'activa' ORDER BY fecha_publicacion DESC";
$ofertas = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ofertas de Empleo</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f5f7;
            margin: 0;
            padding: 30px;
        }
        h1 { margin: 0; font-size: 24px; }
        .sub { color: #666; margin-bottom: 30px; }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-box input {
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 300px;
        }

        .filters {
            display: flex;
            gap: 10px;
        }

        select, .filter-btn {
            padding: 10px 14px;
            border: 1px solid #ccc;
            background: white;
            border-radius: 6px;
            cursor: pointer;
        }

        .listado {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .card h2 { margin: 0; }
        .card p { margin: 6px 0; }
        .card button {
            margin-top: 10px;
            padding: 10px 18px;
            background: #111;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .no-results {
            margin-top: 40px;
            text-align: center;
            color: #888;
        }


        
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


        body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f5f7fa;
    color: #333;
margin: 10px;
    padding: 0;
}

h1 {
    font-size: 28px;
    color: #111;
    margin-bottom: 5px;
}

p.sub, .sub {
    color: #666;
    font-size: 15px;
    margin-top: 0;
    margin-bottom: 20px;
}

.container {
    max-width: 1000px;
    margin: auto;
    padding: 30px 20px;
}

.card {
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-3px);
}

.card h2 {
    font-size: 20px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card p {
    margin: 8px 0;
    color: #444;
    font-size: 15px;
}

.badge {
    background-color: #007bff;
    color: white;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 12px;
}

.aplicar {
    background-color: #111;
    color: white;
    border: none;
    padding: 8px 16px;
    font-size: 14px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 12px;
    transition: background-color 0.3s ease;
}

.aplicar:hover {
    background-color: #333;
}

.filtros {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 30px;
}

.filtros select {
    padding: 8px 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.paginacion {
    text-align: center;
    margin-top: 30px;
}

.paginacion a, .paginacion strong {
    padding: 6px 12px;
    margin: 0 4px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    font-size: 14px;
}

.paginacion a {
    background-color: #f0f0f0;
    color: #007bff;
}

.paginacion strong {
    background-color: #007bff;
    color: white;
}

@media (max-width: 768px) {
    .card h2 {
        font-size: 18px;
        flex-direction: column;
        align-items: flex-start;
    }

    .filtros {
        flex-direction: column;
        align-items: flex-start;
    }

    .filtros label {
        width: 100%;
        margin-bottom: 10px;
    }

    .aplicar {
        width: 100%;
    }
}




    </style>

    <script>
        function aplicarOferta(idOferta, yaPostulado) {
            if (yaPostulado) {
                alert("Ya te has postulado a esta oferta.");
            } else {
                window.location.href = "formulario_postulacion.php?oferta_id=" + idOferta;
            }
        }

        function filtrarOfertas() {
            const input = document.getElementById("buscador").value.toLowerCase();
            const tarjetas = document.querySelectorAll(".card");
            let visibles = 0;

            tarjetas.forEach(card => {
                const texto = card.textContent.toLowerCase();
                const visible = texto.includes(input);
                card.style.display = visible ? "block" : "none";
                if (visible) visibles++;
            });

            document.getElementById("contador").textContent = `Mostrando ${visibles} ofertas`;
        }

                function toggleMenu() {
            var menu = document.getElementById('profile-menu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</head>
<body>

    <div class="profile-container" onclick="toggleMenu()">
        <div class="profile-icon">👤</div>
        <div id="profile-menu" class="profile-menu">
            <a href="panel_candidato.php">Panel</a>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>
    <h1>Ofertas de Empleo Destacadas - Miguel Caballero SAS</h1>
    <p class="sub">Encuentra tu próxima oportunidad profesional</p>

    <div class="topbar">
        <div class="search-box">
            <input type="text" id="buscador" onkeyup="filtrarOfertas()" placeholder="Buscar por puesto, empresa o palabras clave...">
        </div>
        <div class="filters">
            <select>
                <option>Más recientes</option>
                <option>Más antiguas</option>
            </select>
            <button class="filter-btn"> Filtros</button>
        </div>
    </div>

    <p id="contador">Mostrando <?php echo $ofertas->num_rows; ?> ofertas</p>

    <div class="listado">
        <?php if ($ofertas && $ofertas->num_rows > 0): ?>
            <?php while($oferta = $ofertas->fetch_assoc()): ?>
                <?php
                $ya_postulado = false;
                if ($correo_usuario) {
                    $stmt = $conn->prepare("SELECT id_postulacion FROM postulaciones WHERE id_oferta = ? AND correo = ?");
                    $stmt->bind_param("is", $oferta['id_oferta'], $correo_usuario);
                    $stmt->execute();
                    $stmt->store_result();
                    $ya_postulado = $stmt->num_rows > 0;
                    $stmt->close();
                }
                ?>
                <div class="card">
                    <h2><?php echo htmlspecialchars($oferta['titulo']); ?></h2>
                    <p><?php echo htmlspecialchars($oferta['descripcion']); ?></p>
                    <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($oferta['ubicacion']); ?> | <strong>Departamento:</strong> <?php echo htmlspecialchars($oferta['departamento']); ?></p>
                    <p><strong>Salario:</strong> $<?php echo htmlspecialchars($oferta['salario']); ?></p>
                    <p><strong>Publicado el:</strong> <?php echo date('d M Y', strtotime($oferta['fecha_publicacion'])); ?></p>
                    
                    <button onclick="aplicarOferta(<?php echo $oferta['id_oferta']; ?>, <?php echo $ya_postulado ? 'true' : 'false'; ?>)">Aplicar</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-results">No se encontraron ofertas disponibles.</p>
        <?php endif; ?>
    </div>
</body>
</html>
