<?php
include '../config/conexion.php';

// Obtener los datos del usuario
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Escapar el valor de 'id' para evitar inyecciones SQL
    $id = $conn->real_escape_string($id);
    
    $query = "SELECT * FROM usuarios WHERE id = '$id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Obtener el usuario como un array asociativo
        $usuario = $result->fetch_assoc();
        echo json_encode($usuario); // Retornar los datos en formato JSON
    } else {
        echo json_encode(["error" => "Usuario no encontrado"]);
    }
}

// Cerrar la conexión
$conn->close();
?>
