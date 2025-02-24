<?php
// Incluir el archivo de conexión
include('config/conexion.php');

// Variable para manejar mensajes
$mensaje = '';
$tipoMensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $cedula = $_POST['cedula'];

    // Verificar si la cédula ya existe en la base de datos
    $checkCedulaQuery = "SELECT * FROM usuarios WHERE cedula = '$cedula'";
    $result = $conn->query($checkCedulaQuery);

    if ($result->num_rows > 0) {
        // Si la cédula ya existe, preparar mensaje de error
        $mensaje = 'La cédula ya está registrada.';
        $tipoMensaje = 'error';
    } else {
        // Se guarda la hora actual en formato 24 horas (sin AM/PM)
        $created_at = date("Y-m-d H:i:s");  // Fecha y hora actual con formato completo
        
        // Insertar los datos en la base de datos
        $sql = "INSERT INTO usuarios (nombre, cedula, created_at) 
                VALUES ('$nombre', '$cedula', '$created_at')";

        if ($conn->query($sql) === TRUE) {
            // Preparar mensaje de éxito
            $mensaje = 'Nuevo usuario registrado con éxito';
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error: " . $sql . "<br>" . $conn->error;
            $tipoMensaje = 'error';
        }
    }

    // Cerrar la conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - TF Auditores</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for additional responsiveness */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex-grow: 1;
        }
        @media (max-width: 640px) {
            .form-container {
                width: 90%;
                margin: 1rem auto;
                padding: 1.5rem;
            }
            input {
                font-size: 14px;
            }
            .logo {
                height: 60px;
            }
        }
        @media (min-width: 641px) {
            .form-container {
                max-width: 500px;
                margin: 2rem auto;
            }
        }
    </style>
</head>
<body class="bg-[#E1EEE2] flex flex-col min-h-screen">
    <!-- Modal -->
    <div id="alertModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-xl text-center max-w-sm w-full mx-4">
            <div id="alertIcon" class="mb-4 flex justify-center"></div>
            <p id="alertMessage" class="text-lg mb-4"></p>
            <button onclick="closeModal()" class="bg-red-500 hover:bg-red-500 text-white font-bold py-2 px-4 rounded">
                Cerrar
            </button>
        </div>
    </div>

    <header class="bg-white shadow-sm">
        <div class="container mx-auto flex flex-wrap p-4 flex-col md:flex-row items-center">
            <nav class="md:ml-auto md:mr-auto flex flex-wrap items-center justify-center">
                <a href="/index.php">
                    <img src="/static/img/TF.png" alt="Logo-TF" class="h-16 md:h-20 logo">
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md p-6 rounded-lg shadow-xl bg-white form-container">
            <h2 class="text-2xl md:text-3xl font-bold mb-6 text-gray-700 text-center">Registro de Usuario</h2>
            <form action="registro.php" method="POST" class="space-y-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre y Apellidos</label>
                    <input type="text" id="nombre" name="nombre" 
                        class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" 
                        required minlength="3" maxlength="50" 
                        pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" 
                        title="Solo letras, tildes, ñ y espacios son permitidos" 
                        placeholder="Ejemplo: Sergio Quiroga Baez">
                </div>
                <div>
                    <label for="cedula" class="block text-sm font-medium text-gray-700">Cédula</label>
                    <input type="text" id="cedula" name="cedula" 
                        class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" 
                        required minlength="6" maxlength="10" 
                        pattern="\d*" 
                        title="Solo números" 
                        placeholder="Ejemplo: 88936568">
                </div>
                <div class="text-center">
                    <button type="submit" 
                        class="w-full bg-green-600 hover:bg-lime-500 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200">
                        Registrarse
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white text-gray-600 body-font">
        <div class="container px-5 py-4 mx-auto flex items-center sm:flex-row flex-col">
            <p class="text-xs sm:text-sm text-gray-700 sm:ml-4 sm:pl-4 sm:border-l-2 sm:border-gray-200 sm:py-2 sm:mt-0 mt-4">
                &copy; 2024 TF AUDITORES
            </p>
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
    // Check if there's a message to display
    document.addEventListener('DOMContentLoaded', function() {
        const mensaje = '<?php echo $mensaje; ?>';
        const tipoMensaje = '<?php echo $tipoMensaje; ?>';
        
        if (mensaje) {
            showModal(mensaje, tipoMensaje);
        }
    });

    function showModal(message, type = 'error') {
        const modal = document.getElementById('alertModal');
        const messageEl = document.getElementById('alertMessage');
        const iconEl = document.getElementById('alertIcon');

        // Set message
        messageEl.textContent = message;

        // Set icon and color based on message type
        if (type === 'success') {
            iconEl.innerHTML = `
                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
            messageEl.classList.remove('text-red-600');
            messageEl.classList.add('text-green-600');
        } else {
            iconEl.innerHTML = `
                <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
            messageEl.classList.remove('text-green-600');
            messageEl.classList.add('text-red-600');
        }

        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('alertModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');

        // Redirigir según el tipo de mensaje
        const messageEl = document.getElementById('alertMessage');
        if (messageEl.classList.contains('text-green-600')) {
            window.location.href = 'index.php';  // Redirigir a index.php si es éxito
        } else {
            window.location.href = 'ingresar.php';  // Redirigir a ingresar.php si es error
        }
    }
</script>

</body>
</html>