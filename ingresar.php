<?php
include('config/conexion.php');
date_default_timezone_set('America/Bogota');

// Procesar la solicitud solo si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener la cédula desde la solicitud POST
    $cedula = $_POST['cedula'] ?? null;
    if (!$cedula) {
        echo json_encode([
            'error' => true,
            'mensaje' => "Por favor, proporciona una cédula."
        ]);
        exit();
    }
    
    $hora_actual = date('Y-m-d H:i:s');

    // Verificar si el número de cédula existe en la base de datos
    $stmt = $conn->prepare("SELECT id, nombre, hora_ingreso FROM usuarios WHERE cedula = ? ORDER BY hora_ingreso DESC LIMIT 1");
    $stmt->bind_param("s", $cedula); // Vinculamos la cédula como string
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Depuración: Verificar si se obtuvo el usuario
    if (!$usuario) {
        echo json_encode([
            'error' => false,
            'mensaje' => "La cédula no existe en nuestra base de datos. Por favor, regístrala primero."
        ]);
        exit();
    }

    // Si el usuario existe, verificar si han pasado 5 minutos desde su último ingreso
    $ultima_hora_ingreso = strtotime($usuario['hora_ingreso']);
    $tiempo_actual = strtotime($hora_actual);

    // Si el último ingreso fue hace menos de 5 minutos (300 segundos)
    if (($tiempo_actual - $ultima_hora_ingreso) < 300) {
        echo json_encode([
            'mensaje' => "El usuario ya registró un ingreso. Espere 5 minutos antes de intentar nuevamente."
        ]);
        exit();
    }

    // Si no hay restricción de tiempo, registrar el nuevo ingreso
    $nombre = $usuario['nombre']; // Usar el nombre existente
    $insertStmt = $conn->prepare("INSERT INTO usuarios (cedula, nombre, hora_ingreso) VALUES (?, ?, ?)");
    $insertStmt->bind_param("sss", $cedula, $nombre, $hora_actual); // Vinculamos cédula, nombre y hora de ingreso
    $insertStmt->execute();

    echo json_encode([
        'existe' => true,
        'mensaje' => "Ingreso registrado exitosamente el $hora_actual."
    ]);

    $stmt->close();
    $insertStmt->close();
    // Cerrar la conexión
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro de Ingreso</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <meta name="author" content="Sergio Quiroga,Cristian Jiménez">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="icon" href="/static/img/TF.ico" type="image/x-icon">
        <style>
            /* Asegura que el body ocupe toda la altura de la ventana */
            body {
                height: 100vh;
                display: flex;
                flex-direction: column;
            }
            /* Centra el formulario vertical y horizontalmente */
            .content {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
            }
        </style>
    </head>
    <body class="flex items-center justify-center min-h-screen" style="background-color: #E1EEE2;">
        <header class="text-gray-600 body-font w-full bg-white shadow-md">
            <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
                <nav class="md:ml-auto md:mr-auto flex flex-wrap items-center text-base justify-center">
                    <a href="/index.php">
                        <img src="/static/img/TF.png" alt="Logo-TF" class="h-20">
                    </a>
                </nav>
            </div>
        </header>
        <div class="content px-4">
            <div class="p-10 bg-white rounded-lg shadow-xl max-w-md text-center">
                <h2 class="text-3xl font-bold mb-6 text-gray-700">Registro ingreso</h2>
                <p class="mb-4 text-gray-600">Por favor, ingresa tu número de cédula para registrar tu ingreso.</p>
                <form id="form-ingreso">
                    <div class="mb-4">
                        <input type="text" id="numero-usuario" name="cedula" 
                            placeholder="Número de cédula" 
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" 
                            required minlength="6" maxlength="10">
                    </div>
                    <button type="submit" 
                        class="bg-green-600 hover:bg-lime-500 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200">
                        Registrar Ingreso
                    </button>

                    <a href="registro.php">
                        <p class="text-black hover:text-black font-bold cursor-pointer mt-8">
                            No olvides resgistrarte, <span class="text-blue-500 hover:text-blue-500">aquí!</span> 
                        </p>
                        <p class="text-black hover:text-black font-bold cursor-pointer ">si aún no lo has hecho.</p>
                    </a>

                </form>
                <div id="mensaje" class="mt-4 text-gray-700 font-semibold"></div>
            </div>
        </div>
        <script src="/static/js/ingresar.js"></script>
    </body>
</html>
