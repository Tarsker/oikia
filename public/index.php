<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

check_session();
$user = get_logged_in_user();

if (!$user) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Oikia</title>
    <link rel="stylesheet" href="/oikia/assets/css/styles.css?v=1">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Oikia</h2>
        <div class="welcome-message">Hola <?= htmlspecialchars($user['username']) ?>, Bienvenido a Oikia</div>
        <div class="menu">
            <a href="add_property.php">Agregar Propiedad</a>
            <a href="view_properties.php">Ver Propiedades</a>
            <a href="add_expense.php">Agregar Gasto</a>
            <a href="view_expenses.php">Ver Gastos</a>
            <a href="report.php">Generar Reporte</a>
            <?php if ($user['role'] === 'admin'): ?>
                <a href="view_logs.php">Ver Logs</a>
                <a href="admin_requests.php">Administrar Usuarios</a>
                <a href="manage_users.php">Gestionar Usuarios</a>
            <?php endif; ?>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        document.onkeydown = function(e) {
            if (e.key == "F12" || (e.ctrlKey && e.shiftKey && e.key == "I") || (e.ctrlKey && e.key == "U")) {
                e.preventDefault();
                return false;
            }
        };
    </script>
</body>
</html>
