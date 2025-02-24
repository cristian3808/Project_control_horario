$(document).ready(function () {
    // Manejo del formulario de salida
    $("#form-salida").submit(function (event) {
        event.preventDefault(); // Prevenir envío normal del formulario
        const cedula = $("#numero-usuario").val();
        const mensajeDiv = $("#mensaje");

        // Verificar si el campo está vacío
        if (!cedula) {
            mensajeDiv.text("Por favor, ingresa tu número de cédula.")
                .removeClass('text-green-500')
                .addClass('text-red-500');
            return;
        }

        // Realizar la petición AJAX
        $.ajax({
            url: '', // Archivo PHP actual
            type: 'POST',
            data: { cedula: cedula },
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    // Mostrar el mensaje de error
                    mensajeDiv.text(response.mensaje)
                        .removeClass('text-green-500')
                        .addClass('text-red-500');
                    
                    // Redirigir después de 4 segundos
                    setTimeout(function () {
                        window.location.href = '/index.php'; // Redirige después de 4 segundos
                    }, 4000);
                } else if (response.existe) {
                    // Mostrar la hora de salida si se registró correctamente
                    let mensaje = response.mensaje;
                    mensajeDiv.text(mensaje)
                        .removeClass('text-red-500')
                        .addClass('text-green-500');

                    // Redirigir después de 4 segundos
                    setTimeout(function () {
                        window.location.href = '/index.php'; // Redirige después de 4 segundos
                    }, 4000); 
                } else {
                    mensajeDiv.text(response.mensaje)
                        .removeClass('text-green-500')
                        .addClass('text-red-500');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Mensaje de error si algo falla
                mensajeDiv.text("Hubo un error al procesar la solicitud. Intenta nuevamente.")
                    .removeClass('text-green-500')
                    .addClass('text-red-500');

                // Redirigir después de 4 segundos en caso de error
                setTimeout(function () {
                    window.location.href = '/index.php'; // Redirige después de 4 segundos
                }, 4000);
                console.error("Detalles del error:", jqXHR.responseText);
            }
        });
    });
});
