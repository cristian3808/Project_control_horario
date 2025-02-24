function cerrarSesion() {
    // Borrar la sesión
    sessionStorage.clear(); // O localStorage según lo que estés utilizando

    // Manipular el historial para evitar que se pueda regresar
    window.history.pushState(null, '', window.location.href);
    window.history.forward(); // Mueve el historial hacia adelante, "bloqueando" el botón Atrás

    // Redirigir al login
    window.location.href = 'login.php'; // Ajusta la ruta si es necesario
}

// Bloquear el retroceso del navegador
window.onpopstate = function() {
    // Si el usuario intenta ir atrás, lo redirigimos al login
    window.location.href = 'login.php'; // Ajusta la ruta si es necesario
};

// Función para mostrar un modal
function mostrarModal(tipo, id = null) {
    if (tipo === 'nuevo') {
        document.getElementById('modal-nuevo').classList.remove('hidden');
    } else if (tipo === 'eliminar') {
        document.getElementById('id-eliminar').value = id;
        document.getElementById('modal-eliminar').classList.remove('hidden');
    } else if (tipo === 'editar') {
        // Cargar datos del usuario para editar
        fetch(`editar_usuario.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('id-editar').value = data.id;
                document.getElementById('nombre-editar').value = data.nombre;
                document.getElementById('cedula-editar').value = data.cedula;
                document.getElementById('modal-editar').classList.remove('hidden');
            });
    }
}

// Función para cerrar un modal
function cerrarModal(tipo) {
    document.getElementById('modal-' + tipo).classList.add('hidden');
}