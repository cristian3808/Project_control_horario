<?php
include('config/conexion.php');

// Procesar la solicitud solo si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'] ?? null;

    if (!$cedula) {
        echo json_encode([
            'error' => true,
            'mensaje' => "Por favor, proporciona una cédula."
        ]);
        exit();
    }

    // Buscar el último registro del usuario por cédula
    $stmt = $conn->prepare("
        SELECT id, hora_ingreso, hora_salida 
        FROM usuarios 
        WHERE cedula = ? 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $stmt->bind_param("s", $cedula); // Vinculamos la cédula como string
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Verificar si el usuario tiene un registro de ingreso
    if (!$usuario || !$usuario['hora_ingreso']) {
        echo json_encode([
            'error' => true,
            'mensaje' => "No se encontró un registro de ingreso para esta cédula. No puedes registrar una salida sin un ingreso previo."
        ]);
        exit();
    }

    if ($usuario) {
        // Verificamos si ya se registró una salida
        if ($usuario['hora_salida']) {
            echo json_encode([
                'existe' => true,
                'mensaje' => "La salida ya fue registrada anteriormente a las " . $usuario['hora_salida'] . "."
            ]);
            exit();
        }

        // Verificar si han pasado al menos 5 minutos desde el último ingreso
        date_default_timezone_set('America/Bogota');
        $hora_ingreso = strtotime($usuario['hora_ingreso']);
        $hora_actual = time(); // Hora actual en formato Unix timestamp

        // Calculamos la diferencia en segundos entre la hora de ingreso y la hora actual
        $diferencia = $hora_actual - $hora_ingreso;

        if ($diferencia < 300) { // Si la diferencia es menor a 300 segundos (5 minutos)
            echo json_encode([
                'error' => true,
                'mensaje' => "No puedes registrar la salida aún. Deben pasar al menos 5 minutos desde tu ingreso."
            ]);
            exit();
        }

        // Si han pasado 5 minutos o más, registramos la hora de salida
        $hora_salida = date('Y-m-d H:i:s'); // Hora actual en formato de 24 horas
        $updateStmt = $conn->prepare("UPDATE usuarios SET hora_salida = ? WHERE id = ?");
        $updateStmt->bind_param("si", $hora_salida, $usuario['id']); // Vinculamos la hora de salida y el id
        $updateStmt->execute();

        echo json_encode([
            'existe' => true,
            'mensaje' => "Salida registrada exitosamente a las $hora_salida.",
            'hora_salida' => $hora_salida
        ]);
    } else {
        // Si no se encuentra el usuario en la base de datos
        echo json_encode([
            'existe' => false,
            'mensaje' => "No se encontró un registro de ingreso para esta cédula."
        ]);
    }

    $stmt->close();
    $updateStmt->close();
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
        <title>Registro de Salida</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
        <meta name="author" content="Sergio Quiroga,Cristian Jiménez">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="icon" href="/static/img/TF.ico" type="image/x-icon">
    </head>
    <body class="flex items-center justify-center min-h-screen" style="background-color: #E1EEE2;">
        <header class="text-gray-600 body-font w-full bg-white shadow-md fixed top-0 left-0 z-10">
            <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
                <nav class="md:ml-auto md:mr-auto flex flex-wrap items-center text-base justify-center">
                    <a href="/index.php">
                        <img src="/static/img/TF.png" alt="Logo-TF" class="h-20">
                    </a>
                </nav>
            </div>
        </header>
        <div class="flex items-center justify-center min-h-screen pt-20 px-4">
            <div class="p-10 bg-white rounded-lg shadow-xl max-w-md text-center w-full">
                <h2 class="text-3xl font-bold mb-6 text-gray-700">Registro salida</h2>
                <p class="mb-4 text-gray-600">Por favor, ingresa tu número de cédula para registrar tu salida.</p>
                <form id="form-salida">
                    <div class="mb-4">
                        <input type="text" id="numero-usuario" name="numero_usuario" placeholder="Número de cédula" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required minlength="6" maxlength="10">
                    </div>
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200">
                        Registrar Salida
                    </button>
                </form>
                <!-- Mensaje para ver si está el usuario o no -->
                <div id="mensaje" class="mt-4 text-gray-700 font-semibold"></div>
            </div>
        </div>
        <script src="/static/js/salida.js"></script>
    </body>
</html>
