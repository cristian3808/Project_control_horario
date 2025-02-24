$(document).ready(function() {
    // Evento para manejar el formulario de ingreso
    $('#form-ingreso').submit(function(event) {
        event.preventDefault(); // Evita que se envíe el formulario por defecto
        
        // Obtener el número de cédula ingresado
        let cedula = $('#numero-usuario').val();
            $.ajax({
            url: '', 
            method: 'POST',
            data: { cedula: cedula },
            dataType: 'json',
            success: function(response) {
                let mensaje = $('#mensaje');
                if (response.error) {
                    mensaje.text('Error: ' + response.mensaje).css('color', 'red');
                    setTimeout(function() {
                        window.location.href = '/index.php';
                    }, 4000); 
                } else if (response.existe) {
                    mensaje.text(response.mensaje).css('color', 'green');
                    setTimeout(function() {
                        window.location.href = '/index.php';
                    }, 4000); 
                } else {
                    mensaje.text(response.mensaje).css('color', 'red');
                    setTimeout(function() {
                        window.location.href = '/index.php';
                    }, 4000);
                }
            },
            error: function() {
                $('#mensaje').text('Hubo un error al procesar la solicitud.').css('color', 'red');
            }
        });
    });
});
