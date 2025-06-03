
<?php
include_once __DIR__ . '/../backend/db/db.php';
 // Incluye el archivo de conexión a la base de datos

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = isset($_POST['username']) ? $_POST['username'] : '';
    $contrasena = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($usuario) && !empty($contrasena)) {
        $sql = "SELECT * FROM usuarios WHERE correo = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Verifica si la contraseña ingresada coincide
                if (password_verify($contrasena, $row['contrasena'])) {

                    $_SESSION['username'] = $usuario;
                    $_SESSION['role'] = $row['rol'];
                    // Guardar datos esenciales en la sesión
                    $_SESSION['usuario'] = [
                        'id' => $row['id'],
                        'nombre' => $row['nombre'],
                        'dni' => $row['dni'],
                        'telefono' => $row['telefono'],
                        'email' => $row['correo'],
                        'foto' => $row['foto'], // Asegúrate de tener esta columna en la tabla
                        'rol' => $row['rol']
                    ];

                    // Redirigir según el rol
                    switch ($row['rol']) {
                        case 'admin':
                            header("Location: admin.php");
                            break;
                        case 'employee':
                            header("Location: empleados.php");
                            break;
                        case 'gerente':
                            header("Location: admin_ofertas.php");
                            break;
                        case 'supervisor':
                            header("Location: admin.php");
                            break;
                        default:
                            header("Location: ofertas.php");
                            break;
                    }
                    exit;
                } else {
                    echo "Contraseña incorrecta.";
                }
            } else {
                echo "No se encontró una cuenta con ese correo.";
            }

            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Por favor, complete todos los campos.";
    }
}
?>
