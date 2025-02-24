<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Horario TF</title>
        <meta name="author" content="Sergio Quiroga,Cristian Jiménez">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="icon" href="/static/img/TF.ico" type="image/x-icon">
        <style>
    /* Mantén el fondo y estilo del cuerpo */
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

    /* Ajustes responsivos */
    @media (max-width: 640px) {
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            z-index: 10;
            box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
        }

        main {
            flex: 1;
            overflow-y: auto; /* Solo desplaza el contenido si es necesario */
        }
    }

    @media (min-width: 641px) {
        footer {
            position: static;
        }
    }
</style>
    </head>
    <body class="flex flex-col min-h-screen background-color">
        <header class="text-gray-600 body-font w-full bg-white shadow-sm">
            <div class="container mx-auto flex flex-wrap p-4 flex-col md:flex-row items-center">
                <a href="https://tfauditores.com/" class="flex-shrink-0">
                    <img src="/static/img/TF.png" alt="Logo-TF" class="h-14 md:h-20">
                </a>
                <nav class="md:ml-auto md:mr-auto flex flex-wrap items-center text-base justify-center"></nav>
                <!-- Botón visible solo en pantallas medianas en adelante -->
                <button class="hidden md:inline-flex items-center bg-green-600 hover:bg-lime-500 text-white font-bold py-2 px-4 rounded-lg shadow-md transform transition hover:scale-105 duration-200" onclick="goToPage('admin')">
                    <strong>Iniciar Sesión</strong>
                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-1" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </header>

        <main class="flex-grow flex items-center justify-center px-4 -mt-20 ">
            <div class="overlay p-6 md:p-10 rounded-lg shadow-xl text-center w-full max-w-md">
                <h2 class="text-2xl md:text-3xl font-bold mb-4 md:mb-6 text-gray-700">¡Bienvenido a TF!</h2>
                <p class="mb-6 md:mb-8 text-gray-600 text-sm md:text-base">Nos alegra tenerte aquí. Por favor, registra tu ingreso y salida para que podamos hacer un seguimiento de tu jornada laboral. Gracias por tu dedicación y esfuerzo diario.</p>      
                <div class="button-container flex gap-4 justify-center flex-col md:flex-row">
                    <button id="btn-ingresar" type="button" class="bg-green-600 hover:bg-lime-500 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200 md:flex-1" onclick="goToPage('ingresar')">
                        Ingreso
                    </button>
                    <button id="btn-salida" type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transform transition hover:scale-105 duration-200 md:flex-1" onclick="goToPage('salida')">
                        Salida
                    </button>
                </div>

                <div class="mt-6">
                    <p class="text-black hover:text-black font-bold cursor-pointer" onclick="goToPage('registrarse')">
                        No estás registrado, <span class="text-blue-500 hover:text-blue-500">regístrate aquí</span>
                    </p>
                </div>

                <p class="mt-6 md:mt-8 text-xs md:text-sm text-gray-400">Estamos aquí para apoyarte en cada paso del camino.</p>
            </div>
        </main>

        <footer class="bg-white text-gray-600 body-font">
        <div class="container px-5 py-4 mx-auto flex items-center sm:flex-row flex-col">
            <p class="text-xs sm:text-sm text-gray-700 sm:ml-4 sm:pl-4 sm:border-l-2 sm:border-gray-200 sm:py-2 sm:mt-0 mt-auto">
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

        <script src="/static/js/index.js"></script>
    </body>
</html>