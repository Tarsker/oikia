<?php
require_once __DIR__ . '/../config/config.php';  // Asegúrate de que la ruta es correcta

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

function execute_query($query, $params = []) {
    global $conn;
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Error en la preparación de la declaración: ' . $conn->error);
    }

    if ($params) {
        $types = str_repeat('s', count($params)); // Asumimos que todos los parámetros son strings
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        return true; // La consulta fue de tipo INSERT, UPDATE, DELETE, etc.
    }

    $stmt->close();

    return $result;
}
?>
