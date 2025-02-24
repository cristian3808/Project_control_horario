<?php
session_start();
include '../config/conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // Redirige al login si no está autenticado
    exit();
}

// Inicializar variables de filtro
$cedulaFilter = isset($_GET['cedula']) ? trim($_GET['cedula']) : '';
$fechaFilter = isset($_GET['fecha']) ? trim($_GET['fecha']) : '';

// Construir la consulta SQL según el filtro
$query = "SELECT nombre, cedula, hora_ingreso, hora_salida, created_at FROM usuarios WHERE 1=1";
$params = [];
$paramTypes = ""; // Tipos de parámetros para MySQLi

// Filtrar solo por cédula si se presiona el botón de cédula
if (!empty($cedulaFilter) && isset($_GET['filtrar_cedula'])) {
    $query .= " AND cedula = ?";
    $params[] = $cedulaFilter;
    $paramTypes .= "s";
}

// Filtrar solo por fecha si se presiona el botón de fecha
if (!empty($fechaFilter) && isset($_GET['filtrar_fecha'])) {
    $query .= " AND DATE(created_at) = ?";
    $params[] = $fechaFilter;
    $paramTypes .= "s";
}

$query .= " ORDER BY created_at ASC";

// Paginación
$registrosPorPagina = 17;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($paginaActual - 1) * $registrosPorPagina;

// Contar el total de registros con el mismo filtro
$sqlContar = "SELECT COUNT(*) as total FROM usuarios WHERE 1=1";
$paramsCount = [];
$paramTypesCount = "";

// Aplicar filtros en la consulta de conteo
if (!empty($cedulaFilter)) {
    $sqlContar .= " AND cedula = ?";
    $paramsCount[] = $cedulaFilter;
    $paramTypesCount .= "s";
}
if (!empty($fechaFilter)) {
    $sqlContar .= " AND DATE(created_at) = ?";
    $paramsCount[] = $fechaFilter;
    $paramTypesCount .= "s";
}

// Preparar y ejecutar la consulta de conteo
$stmtContar = $conn->prepare($sqlContar);
if ($paramTypesCount) {
    $stmtContar->bind_param($paramTypesCount, ...$paramsCount);
}
$stmtContar->execute();
$resultContar = $stmtContar->get_result();
$totalRegistros = $resultContar->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
$stmtContar->close();

// Obtener los usuarios de la base de datos para la página actual
$query .= " LIMIT ?, ?";
$params[] = $inicio;
$params[] = $registrosPorPagina;
$paramTypes .= "ii"; // 'i' para enteros

// Preparar y ejecutar la consulta principal
$stmt = $conn->prepare($query);
if ($paramTypes) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$usuarios = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Ordenar el array $usuarios por la columna 'created_at' de manera descendente
usort($usuarios, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$conn->close(); // Cerrar la conexión
?>

<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Horario TF</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <meta name="author" content="Sergio Quiroga,Cristian Jiménez">
        <link rel="icon" href="/static/img/TF.ico" type="image/x-icon">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <style>
        .background-color {
            background-color: #E1EEE2;
            background-size: cover;
            background-position: center;
        }
        .overlay {
            background: rgba(255, 255, 255, 0.8);
        }
    </style>
    <body class="flex flex-col items-center min-h-screen background-color">
        <header class="text-gray-600 body-font w-full bg-white">
            <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center justify-between">
                <img src="/static/img/TF.png" alt="Logo-TF" class="h-20">
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <a href="/admin/admin.php" class="hidden md:inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200 h-10">
                        <img src="/static/img/volver.svg" alt="Volver" class="h-5 mr-2">
                        <span class="tracking-wide">Volver</span>
                    </a>
                    <!-- Botón de Cerrar Sesión -->
                    <a href="logout.php" class="hidden md:inline-flex items-center bg-green-600 hover:bg-lime-500 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200 mt-4 md:mt-0 h-10">
                        <img src="/static/img/cerrar.svg" alt="Cerrar Sesión" class="h-5 mr-2">
                        <span class="tracking-wide">Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </header>
        <div class="container mx-auto p-4 bg-white shadow-md rounded-lg mt-6">
            <div class="flex space-x-4 mb-4">
                <!-- Formulario de filtro por fecha -->
                <form method="GET" class="flex items-center space-x-2">
                    <label for="fecha">Filtrar por fecha:</label>
                    <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($fechaFilter) ?>" class="border border-gray-300 rounded px-2 py-1">
                    <button type="submit" name="filtrar_fecha" class="flex items-center justify-center bg-green-600 hover:bg-lime-500 text-white font-bold py-2 px-4 rounded-lg shadow-md transition hover:scale-105 duration-200">
                        <img src="../static/img/fecha.svg" alt="Filtrar" class="h-6 w-6"> <!-- Se hace más grande -->
                    </button>
                </form>
                <!-- Formulario de filtro por cédula -->
                <form method="GET" class="flex items-center space-x-2">
                    <label for="cedula">Cédula:</label>
                    <input placeholder="Número de cédula" type="number" id="cedula" name="cedula" value="<?= htmlspecialchars($cedulaFilter) ?>" class="border border-gray-300 rounded px-2 py-1" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);"  >
                    <button type="submit" name="filtrar_cedula" class="bg-green-600 hover:bg-lime-500 text-white font-bold py-2 px-4 rounded-lg shadow-md transition hover:scale-105 duration-200">
                        <img src="../static/img/buscar.svg" alt="Filtrar" class="h-6 w-6"> <!-- Se hace más grande -->
                    </button>
                </form>
                <a href="generar_pdf.php?cedula=<?= urlencode($cedulaFilter) ?>&fecha=<?= urlencode($fechaFilter) ?>" target="_blank">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 hover:scale-105">
                        Generar PDF
                    </button>
                </a>
            </div>
            <table class="table-auto w-full bg-white shadow-md rounded">
                <thead>
                    <tr class="bg-green-600 text-white">
                        <th class="px-4 py-2">Nombre y Apellido</th>
                        <th class="px-4 py-2">Cédula</th>
                        <th class="px-4 py-2">Fecha de Registro</th>
                        <th class="px-4 py-2">Ingreso</th>
                        <th class="px-4 py-2">Salida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <?php 
                            // Lógica para hora_salida
                            if (empty($usuario['hora_ingreso'])) {
                                // Si no hay hora_ingreso, mostrar "registrado" en hora_salida
                                $horaSalida = 'Registro';
                            } elseif (empty($usuario['hora_salida'])) {
                                // Si hay hora_ingreso pero no hora_salida, mostrar "00:00"
                                $horaSalida = '00:00';
                            } else {
                                // Si ambas tienen valores, mostrar la hora de salida formateada
                                $horaSalida = date('h:i A', strtotime($usuario['hora_salida']));
                            }
                        ?>
                        <tr class="border-b hover:bg-gray-200">
                            <td class="px-4 py-2 text-center"><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <td class="px-4 py-2 text-center"><?= htmlspecialchars($usuario['cedula']) ?></td>
                            <td class="px-4 py-2 text-center"><?= htmlspecialchars(date('Y-m-d', strtotime($usuario['created_at']))) ?></td>
                            <td class="px-4 py-2 text-center">
                                <?= htmlspecialchars($usuario['hora_ingreso'] ? date('h:i A', strtotime($usuario['hora_ingreso'])) : 'Registro') ?>
                            </td>
                            <td class="px-4 py-2 text-center"><?= htmlspecialchars($horaSalida) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Paginación -->
        <div class="flex justify-center space-x-2 mt-4">
            <?php if ($paginaActual > 1): ?>
                <a href="?pagina=<?= $paginaActual - 1 ?>" class="px-4 py-2 bg-green-600 text-white rounded">Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="?pagina=<?= $i ?>" class="px-4 py-2 <?= $i == $paginaActual ? 'bg-green-600 text-white' : 'bg-green-600 text-white' ?> rounded"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($paginaActual < $totalPaginas): ?>
                <a href="?pagina=<?= $paginaActual + 1 ?>" class="px-4 py-2 bg-green-600 text-white rounded">Siguiente</a>
            <?php endif; ?>
        </div>
        <script>
            function cerrarSesion() {
                window.location.href = "/logout.php";
            }
        </script>
    </body>
</html>
