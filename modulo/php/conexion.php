<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$servername = "junction.proxy.rlwy.net"; // Cambia esto si tu servidor de base de datos no está en localhost
$username = "root"; // Reemplaza con tu nombre de usuario de la base de datos
$password = "mTNzDjxBAPIYJJMGxvzGhNbBWcOYUScZ"; // Reemplaza con tu contraseña de la base de datos
$dbname = "Telemedicina"; // Reemplaza con el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
?>
mysql://root:mTNzDjxBAPIYJJMGxvzGhNbBWcOYUScZ@junction.proxy.rlwy.net:54837/railway