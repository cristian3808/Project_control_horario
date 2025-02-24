// Obtener la hora actual
const now = new Date();
const hours = now.getHours();

// Seleccionar los botones
const btnIngresar = document.getElementById('btn-ingresar');
const btnSalida = document.getElementById('btn-salida');

// Función de redirección según el botón presionado
function goToPage(action) {
    if (action === 'ingresar' && !btnIngresar.disabled) {
        window.location.href = '/ingresar.php'; 
    } else if (action === 'salida' && !btnSalida.disabled) {
        window.location.href = '/salida.php';  
    } else if (action === 'admin') {  // Agregado para redirigir a la página de administración
        window.location.href = '/admin/login.php';  // Redirecciona a la página de administración
    } else if (action === 'registrarse') {  // Redirige a la página de registro
        window.location.href = '/registro.php';  // Asegúrate de que la URL sea la correcta
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const currentYear = new Date().getFullYear();
    document.getElementById('current-year').textContent = currentYear;
});