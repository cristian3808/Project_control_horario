<?php
session_start();

// Incluir el archivo de conexión a la base de datos
require('../config/conexion.php'); // Incluir la conexión a la base de datos

// Si ya está autenticado, redirige al panel de administración
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: admin.php");
    exit();
}

// Evitar el caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Consultar la base de datos para verificar si el usuario existe
    $sql = "SELECT * FROM admin WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si el usuario existe, verificar la contraseña
        $row = $result->fetch_assoc();
        
        // Verificar la contraseña (se recomienda usar hash para mayor seguridad)
        if ($password === $row['password']) { // Considera usar password_verify si usas hash
            $_SESSION['logged_in'] = true; // Establece la variable de sesión
            $_SESSION['usuario'] = $usuario; // Opcional: Guarda el nombre de usuario
            header("Location: admin.php"); // Redirige al panel de administración
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Horario TF - Login</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
        <meta name="author" content="Sergio Quiroga,Cristian Jiménez">
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            // Evitar que el usuario regrese a la página de login usando el botón "Atrás"
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.pushState(null, null, location.href);
            };
        </script>
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
                <nav class="md:ml-auto md:mr-auto flex flex-wrap items-center text-base justify-center">
                    <a href="/index.php">
                        <img src="/static/img/TF.png" alt="Logo-TF" class="h-20">
                    </a>
                </nav>
            </div>
        </header>
        <div class="overlay p-10 rounded-lg shadow-xl max-w-md text-center mt-20 sm:mt-16 md:mt-32">
            <h2 class="text-3xl font-bold mb-6 text-gray-700">¡Bienvenido a TF!</h2>
            <p class="mb-8 text-gray-600">Por favor ingresa tu usuario y contraseña para continuar.</p>
            <!-- Mostrar errores si existen -->
            <?php if (isset($error)) { ?>
                <div class="text-red-600 mb-4"><?php echo $error; ?></div>
            <?php } ?>

            <form action="" method="POST">
                <div class="mb-4">
                    <label for="usuario" class="block text-left text-gray-700 font-semibold">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Tu usuario" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required minlength="5" maxlength="13">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-left text-gray-700 font-semibold">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Tu contraseña" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required minlength="6" maxlength="10">
                </div>
                <div class="flex gap-4 justify-center">
                    <button type="submit" class="bg-green-600 hover:bg-lime-500 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200 w-full md:w-auto">
                        Iniciar sesión
                    </button>
                </div>
            </form>
        </div>
        <footer class="bg-white text-gray-600 body-font fixed bottom-0 w-full">
            <div class="container px-5 py-8 mx-auto flex items-center sm:flex-row flex-col">
                <p class="text-sm text-gray-700 sm:ml-4 sm:pl-4 sm:border-l-2 sm:border-gray-200 sm:py-2 sm:mt-0 mt-4">© 2024 TF AUDITORES</p>
                <span class="inline-flex sm:ml-auto sm:mt-0 mt-4 justify-center sm:justify-start">
                    <a href="https://www.facebook.com/people/TF-Auditores-y-Asesores-SAS-BIC/100065088457000/" class="text-gray-700 hover:text-blue-500">
                    </a>
                    <a href="https://www.instagram.com/tfauditores/" class="ml-3 text-gray-700 hover:text-pink-500">
                    </a>
                    <a href="https://www.linkedin.com/uas/login?session_redirect=https%3A%2F%2Fwww.linkedin.com%2Fcompany%2F10364571%2Fadmin%2Fdashboard%2F" class="ml-3 text-gray-700 hover:text-blue-300">
                    </a>
                </span>
            </div>
        </footer>
    <script src="/static/js/index.js"></script>
    </body>
</html>
