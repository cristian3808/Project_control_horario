<?php
require('fpdf/fpdf.php');
require('../config/conexion.php'); 

// Obtener los filtros de la URL
$cedulaFilter = isset($_GET['cedula']) ? $_GET['cedula'] : '';
$fechaFilter = isset($_GET['fecha']) ? $_GET['fecha'] : '';

// Construir la consulta SQL según el filtro
$query = "SELECT nombre, cedula, hora_ingreso, hora_salida, created_at FROM usuarios WHERE 1=1";

// Filtrar por cédula si se proporciona
if (!empty($cedulaFilter)) {
    $query .= " AND cedula = ?";
}

// Filtrar por fecha si se proporciona
if (!empty($fechaFilter)) {
    $query .= " AND DATE(created_at) = ?";
}

// Ordenar por fecha de creación
$query .= " ORDER BY created_at ASC";

// Preparar y ejecutar la consulta con mysqli
$stmt = $conn->prepare($query);

// Vincular los parámetros si es necesario
if (!empty($cedulaFilter) && !empty($fechaFilter)) {
    $stmt->bind_param("ss", $cedulaFilter, $fechaFilter);
} elseif (!empty($cedulaFilter)) {
    $stmt->bind_param("s", $cedulaFilter);
} elseif (!empty($fechaFilter)) {
    $stmt->bind_param("s", $fechaFilter);
}

$stmt->execute();
$result = $stmt->get_result();

// Obtener los datos de los usuarios
$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}

// Crear un nuevo PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Agregar logo
$logoPath = '../static/img/TF.png'; // Ruta de la imagen
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 10, 10, 30); // Ajusta las coordenadas y el tamaño según sea necesario
} else {
    // Si la imagen no existe, mostrar mensaje
    $pdf->SetXY(10, 10); // Establece la posición donde el logo iría
    $pdf->Cell(30, 10, 'Logo no encontrado', 0, 1);
}

// Establecer el color de las líneas como blanco solo para las primeras dos filas
$pdf->SetDrawColor(255, 255, 255); // Color blanco para las líneas de las primeras filas

// Crear una tabla para la parte superior (títulos y códigos)
// Mover el cursor justo al lado del logo
$pdf->SetX(40);  // Posición horizontal después del logo, a 40mm

$pdf->SetFont('Arial', 'B', 8);  // Fuente más pequeña para el texto en esta celda
$pdf->Cell(90, 10, 'GESTION DE MEJORA DE CALIDAD', 1, 0, 'C');  // Centrado de esta celda
$pdf->Cell(50, 10, 'Version 3', 1, 1, 'C'); // Alineación a la derecha

// Versión y código en la misma fila, centrado
$pdf->SetFont('Arial', '', 10);
// Mover el cursor a la derecha de nuevo para la siguiente fila de títulos
$pdf->SetX(40);  // Posición horizontal después del logo
$pdf->SetFont('Arial', 'B', 10); // Cambiar a negrita antes de la celda
$pdf->Cell(90, 10, 'CONTROL DE INGRESO Y SALIDA', 1, 0, 'C'); // Centrado de esta celda
$pdf->Cell(50, 10, 'CODIGO: F-GC-14', 1, 1, 'C'); // Centrado de la tercera celda

// Restaurar el color de las líneas a negro para el resto de la tabla
$pdf->SetDrawColor(0, 0, 0); // Color negro para las líneas del resto de la tabla

// Ajuste de la posición vertical para mover la segunda tabla 5mm hacia abajo
$pdf->SetY($pdf->GetY() + 0.2); // Mueve la segunda tabla 5mm hacia abajo
// Crear la tabla de datos con las columnas necesarias
$pdf->SetFont('Arial', 'B', 9);

// Establecer color de fondo gris suave para la primera fila
$pdf->SetFillColor(225,238,226); // Gris suave

// Ajustamos el ancho de las celdas para que ambas partes (izquierda y derecha) tengan el mismo ancho
$pdf->Cell(35, 10, 'FECHA', 1, 0, 'C', true);   // 45mm de ancho
$pdf->Cell(35, 10, 'CEDULA', 1, 0, 'C', true);   // 45mm de ancho
$pdf->Cell(45, 10, 'NOMBRE', 1, 0, 'C', true);   // 45mm de ancho
$pdf->Cell(35, 10, 'HORA DE INGRESO', 1, 0, 'C', true);   // 45mm de ancho
$pdf->Cell(35, 10, 'HORA DE SALIDA', 1, 1, 'C', true);   // 45mm de ancho

// Restaurar el color de fondo a blanco para las siguientes filas
$pdf->SetFillColor(255, 255, 255); // Blanco para las filas de datos

// Datos de los usuarios
$pdf->SetFont('Arial', '', 10);
foreach ($usuarios as $usuario) {
    // Verificar si hora_ingreso es "00:00" o vacío, y poner 'registrado' si es el caso
    if ($usuario['hora_ingreso'] == '00:00' || empty($usuario['hora_ingreso'])) {
        $horaIngreso = 'registrado';
        $horaSalida = 'registrado'; // Si hora_ingreso es "00:00", hora_salida también se pone "registrado"
    } else {
        $horaIngreso = date('h:i a', strtotime($usuario['hora_ingreso']));
        
        // Si hora_salida es diferente de "00:00", usar lo que tiene, de lo contrario, poner "00:00"
        $horaSalida = ($usuario['hora_salida'] != '00:00' && !empty($usuario['hora_salida'])) ? date('h:i a', strtotime($usuario['hora_salida'])) : '00:00';
    }
    
    // Imprimir los datos en el PDF
    $pdf->Cell(35, 10, utf8_decode(date('d-m-Y', strtotime($usuario['created_at']))), 1, 0, 'C', false);
    $pdf->Cell(35, 10, utf8_decode($usuario['cedula']), 1, 0, 'C', false);  // Cédula
    $pdf->Cell(45, 10, utf8_decode($usuario['nombre']), 1, 0, 'C', false);
    $pdf->Cell(35, 10, utf8_decode($horaIngreso), 1, 0, 'C', false);
    $pdf->Cell(35, 10, utf8_decode($horaSalida), 1, 1, 'C', false);
}

// Salida del PDF al navegador
$pdf->Output('D', 'reporte_llegadas_salidas.pdf');

// Cerrar la conexión a la base de datos
$conn->close();
?>
