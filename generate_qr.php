<?php
// Incluir la librería PHP para generar códigos QR
include('phpqrcode/qrlib.php');

// URL fija (estática)
$url = "https://personal.tfauditores.net/index.php";

// Generar el código QR en un archivo temporal
$archivo_qr = 'qrcode.png';
QRcode::png($url, $archivo_qr, QR_ECLEVEL_H, 10);

// Ruta del logo
$archivo_logo = 'static/img/TF.png';

// Cargar el código QR y el logo como imágenes
$qr_imagen = imagecreatefrompng($archivo_qr);
$logo_imagen = imagecreatefrompng($archivo_logo);

// Obtener dimensiones del QR y del logo
$qr_ancho = imagesx($qr_imagen);
$qr_alto = imagesy($qr_imagen);
$logo_ancho = imagesx($logo_imagen);
$logo_alto = imagesy($logo_imagen);

// Calcular dimensiones del logo (reducción para que encaje en el QR)
$nuevo_ancho = $qr_ancho * 0.25; // El logo ocupará el 25% del QR
$nuevo_alto = ($nuevo_ancho / $logo_ancho) * $logo_alto;

// Crear una nueva imagen con las dimensiones del logo redimensionado
$logo_redimensionado = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
imagealphablending($logo_redimensionado, false);
imagesavealpha($logo_redimensionado, true);

// Redimensionar el logo
imagecopyresampled(
    $logo_redimensionado,
    $logo_imagen,
    0, 0, 0, 0,
    $nuevo_ancho, $nuevo_alto,
    $logo_ancho, $logo_alto
);

// Calcular posición para centrar el logo en el QR
$pos_x = ($qr_ancho - $nuevo_ancho) / 2;
$pos_y = ($qr_alto - $nuevo_alto) / 2;

// Insertar el logo redimensionado en el centro del QR
imagecopy(
    $qr_imagen,
    $logo_redimensionado,
    $pos_x, $pos_y,
    0, 0,
    $nuevo_ancho, $nuevo_alto
);

// Guardar la imagen final como un nuevo archivo PNG
$archivo_final = 'qrcode_con_logo.png';
imagepng($qr_imagen, $archivo_final);

// Limpiar recursos
imagedestroy($qr_imagen);
imagedestroy($logo_imagen);
imagedestroy($logo_redimensionado);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Generador de Código QR</title>
        <meta name="author" content="Sergio Quiroga,Cristian Jiménez">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="icon" href="/static/img/TF.ico" type="image/x-icon">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-[#E1EEE2] min-h-screen flex flex-col">
        <header class="bg-white shadow-md w-full">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-center">
                <a href="https://tfauditores.com/">
                    <img src="/static/img/TF.png" alt="Logo-TF" class="h-16 object-top">
                </a>
            </div>
        </header>
        <div class="flex-1 flex justify-center items-center px-6 py-8 mt-[-120px]"> <!-- Usando mt-[-10px] para subir más el contenedor -->
            <div class="border-2 border-green-600 p-4 bg-white p-8 rounded-lg shadow-lg w-full max-w-lg text-center">
                <h1 class="text-3xl font-bold text-gray-700 mb-6">Escanea este Código QR para Registrar tu Ingreso</h1>
                <!-- Mostrar el QR generado -->
                <div class="mb-6">
                    <img src="<?= $archivo_final ?>" alt="Código QR con Logo" class="mx-auto w-1/2 h-auto">
                </div>
                <p class="text-gray-600">
                    Escanea el código QR con tu teléfono móvil para acceder al formulario de registro de ingreso.<br>
                    La URL de este QR cambiará cada día.
                </p>
            </div>
        </div>
        <footer class="bg-white text-gray-600 body-font fixed bottom-0 w-full">
            <div class="container px-5 py-8 mx-auto flex items-center sm:flex-row flex-col">
                <p id="year" class="text-sm text-gray-700 sm:ml-4 sm:pl-4 sm:border-l-2 sm:border-gray-200 sm:py-2 sm:mt-0 mt-4">© <span id="current-year"></span> TF AUDITORES</p>
                <span class="inline-flex sm:ml-auto sm:mt-0 mt-4 justify-center sm:justify-start">
                    <a href="https://www.facebook.com/people/TF-Auditores-y-Asesores-SAS-BIC/100065088457000/" class="text-gray-700 hover:text-blue-500">
                        <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/tfauditores/" class="ml-3 text-gray-700 hover:text-pink-500">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"></path>
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/uas/login?session_redirect=https%3A%2F%2Fwww.linkedin.com%2Fcompany%2F10364571%2Fadmin%2Fdashboard%2F" class="ml-3 text-gray-700 hover:text-blue-300">
                        <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="0" class="w-5 h-5" viewBox="0 0 24 24">
                            <path stroke="none" d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path>
                            <circle cx="4" cy="4" r="2" stroke="none"></circle>
                        </svg>
                    </a>
                </span>
            </div>
        </footer>
    <script>
        // Script para mostrar el año actual en el footer
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
    </body>
</html>
