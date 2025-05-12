<?php
include 'db.php';

$nombre_completo = 'administrador';
$correo = 'admin@rhinnovate.pro';
$contrasena = 'admin';
$rol = 'admin';

// Hashear la contraseña
$hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre_completo, correo, contrasena, rol) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombre_completo, $correo, $hashed_password, $rol);

if ($stmt->execute()) {
    echo "Usuario creado exitosamente.";
} else {
    echo "Error al crear el usuario: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
