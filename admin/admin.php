<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // Redirige al login si no tiene sesión
    exit();
}

// Incluir el archivo de conexión
include '../config/conexion.php';

// Obtener los usuarios con cédulas únicas desde la base de datos
$query = "SELECT * FROM usuarios GROUP BY cedula";
$result = $conn->query($query);
$usuarios = $result->fetch_all(MYSQLI_ASSOC);

// Funciones para eliminar o editar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['eliminar'])) {
        $id = $_POST['id'];

        // Obtener la cédula del usuario que estamos eliminando
        $cedulaQuery = "SELECT cedula FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($cedulaQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $cedula = $usuario['cedula'];

        // Eliminar todos los usuarios con la misma cédula
        $deleteQuery = "DELETE FROM usuarios WHERE cedula = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("s", $cedula);
        $deleteStmt->execute();

        header("Location: admin.php"); // Recargar la página después de la eliminación
        exit();
    }

    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $cedula = $_POST['cedula'];

        // Primero actualizamos todos los usuarios que tengan la misma cédula
        $updateQuery = "UPDATE usuarios SET nombre = ?, cedula = ? WHERE cedula = (SELECT cedula FROM usuarios WHERE id = ?)";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssi", $nombre, $cedula, $id);
        $updateStmt->execute();

        // Luego actualizamos el usuario con el id específico
        $updateQuery = "UPDATE usuarios SET nombre = ?, cedula = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssi", $nombre, $cedula, $id);
        $updateStmt->execute();

        header("Location: admin.php"); // Recargar la página después de la edición
        exit();
    }

    if (isset($_POST['crear'])) {
        $nombre = $_POST['nombre'];
        $cedula = $_POST['cedula'];
        $insertQuery = "INSERT INTO usuarios (nombre, cedula) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ss", $nombre, $cedula);
        $insertStmt->execute();
        header("Location: admin.php"); // Recargar la página después de crear el usuario
        exit();
    }
}

// Cerrar la conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <title>Horario TF</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <meta name="author" content="Sergio Quiroga,Cristian Jiménez">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="icon" href="/static/img/TF.ico" type="image/x-icon">
        <style>
            .background-color {
                background-color: #E1EEE2;
                background-size: cover;
                background-position: center;
            }
            .overlay {
                background: rgba(255, 255, 255, 0.8);
            }
            .disabled-button {
                opacity: 0.5;
                cursor: not-allowed;
            }
        </style>
    </head>
    <body class="flex flex-col items-center min-h-screen background-color">
        <header class="text-gray-600 body-font w-full bg-white">
            <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
                <img src="/static/img/TF.png" alt="Logo-TF" class="h-20">
                <nav class="md:ml-auto md:mr-auto flex flex-wrap items-center text-base justify-center"></nav>
                <a href="/admin/reporte.php" class="hidden md:inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200 mt-4 md:mt-0 h-10 mr-4">
                    <img src="/static/img/reporte.svg" alt="Reporte" class="w-4 h-4 ml-1 mr-2">Reportes
                </a>
                <a href="logout.php" class="hidden md:inline-flex items-center bg-green-600 hover:bg-lime-500 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200 mt-4 md:mt-0 h-10">
                    <img src="/static/img/cerrar.svg" alt="Cerrar Sesión" class="h-5 mr-2">
                    <span class="tracking-wide">Cerrar Sesión</span>
                </a>
            </div>
        </header>
        <div class="container mx-auto p-4 bg-white shadow-md rounded-lg mt-6">
            <div class="flex justify-between mb-4">
                <button onclick="mostrarModal('nuevo')" class="hidden md:inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200 mt-4 md:mt-0 h-10">
                    <img src="/static/img/usuario.svg" alt="Nuevo Usuario" class="w-4 h-4 ml-1 mr-2">Nuevo
                </button>
            </div>
            <table class="min-w-full table-auto">
                <thead class="bg-green-600">
                    <tr class="text-white">
                        <th class="px-4 py-2 text-center">Nombre y Apellido</th>
                        <th class="px-4 py-2 text-center">Cédula</th>
                        <th class="px-4 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2 text-center"><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td class="px-4 py-2 text-center"><?= htmlspecialchars($usuario['cedula']) ?></td>
                        <td class="px-4 py-2 text-center">
                            <button onclick="mostrarModal('editar', <?= $usuario['id'] ?>)" class="text-amber-400 hover:text-amber-600">
                                <i class="fas fa-edit"></i> <!-- Icono de editar -->
                            </button>
                            <button onclick="mostrarModal('eliminar', <?= $usuario['id'] ?>)" class="ml-4 text-red-500 hover:text-red-800">
                                <i class="fas fa-trash-alt"></i> <!-- Icono de eliminar -->
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <hr><br><br>                
    <div id="modal-nuevo" class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white p-8 rounded-lg w-1/3">
            <h2 class="text-xl mb-4 text-center"><strong>Nuevo Empleado</strong></h2>
            <form method="POST" action="admin.php">
                <div class="mb-6">
                    <label for="nombre" class="block text-sm font-medium text-gray-800 mb-2">Nombre y Apellido</label>
                    <input type="text" id="nombre" name="nombre" class="mt-1 block w-full border-green-500 border-2 rounded-lg shadow-sm p-3 text-gray-900 focus:outline-none focus:ring-0" minlength="3" maxlength="30" required>
                </div>
                <div class="mb-6">
                    <label for="cedula" class="block text-sm font-medium text-gray-800 mb-2">Cédula</label>
                    <input type="text" id="cedula" name="cedula" class="mt-1 block w-full border-green-500 border-2 rounded-lg shadow-sm p-3 text-gray-900 focus:outline-none focus:ring-0" minlength="6" maxlength="10" required>
                </div>
                <div class="mb-4 flex justify-center space-x-2">
                    <button type="submit" name="crear" class="bg-green-600 hover:bg-lime-500 text-white py-2 px-4 rounded-lg">Crear</button>
                    <button type="button" onclick="cerrarModal('nuevo')" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal para confirmar eliminación -->
    <div id="modal-eliminar" class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white p-8 rounded-lg w-1/3 text-center"> 
            <h2 class="text-xl mb-4"><strong>¿Seguro que desea eliminar este usuario?</strong></h2>
            <form method="POST" action="admin.php">
                <input type="hidden" id="id-eliminar" name="id">
                <div class="mb-4 flex justify-center space-x-2"> 
                    <button type="submit" name="eliminar" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg">Eliminar</button>
                    <button type="button" onclick="cerrarModal('eliminar')" class="bg-green-600 hover:bg-lime-500 text-white py-2 px-4 rounded-lg">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal para editar usuario -->
    <div id="modal-editar" class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white p-8 rounded-lg w-1/3">
            <h2 class="text-xl mb-4">Editar Usuario</h2>
            <form method="POST" action="admin.php">
                <input type="hidden" id="id-editar" name="id">
                <div class="mb-4">
                    <label for="nombre-editar" class="block text-sm font-medium text-gray-700">Nombre y Apellido</label>
                    <input type="text" id="nombre-editar" name="nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:outline-none focus:ring-2 focus:ring-green-500" required minlength="3" maxlength="50" required>
                </div>
                <div class="mb-4">
                    <label for="cedula-editar" class="block text-sm font-medium text-gray-700">Cédula</label>
                    <input type="text" id="cedula-editar" name="cedula" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:outline-none focus:ring-2 focus:ring-green-500" required minlength="6" maxlength="10" required>
                </div>
                <button type="submit" name="editar" class="bg-green-600 hover:bg-lime-500 text-white py-2 px-4 rounded-lg">Guardar cambios</button>
                <button type="button" onclick="cerrarModal('editar')" class="bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded-lg ml-2">Cancelar</button>
            </form>
        </div>
    </div>
    <script src="/static/js/admin.js"></script>
    </body>
</html>
