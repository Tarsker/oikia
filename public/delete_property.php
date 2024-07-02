<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

// Verificar sesión
check_session();

$id = $_GET['id'];

try {
    $query = "DELETE FROM properties WHERE id = ?";
    execute_query($query, [$id]);
    echo "Propiedad eliminada exitosamente.";
} catch (Exception $e) {
    echo 'Error al eliminar la propiedad: ',  $e->getMessage();
}

header("Location: index.php");
exit;
?>
