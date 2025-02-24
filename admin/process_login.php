<?php
session_start();

// Credenciales de ejemplo (ajusta según tu necesidad)
$username = $_POST['username'];
$password = $_POST['password'];

if ($username === "admin" && $password === "12345") { // Usuario y contraseña válidos
    $_SESSION['logged_in'] = true; // Establece la sesión
    header("Location: admin.php"); // Redirige al panel de administración
    exit();
} else {
    echo "Credenciales incorrectas. <a href='login.php'>Intentar de nuevo</a>";
}
?>
