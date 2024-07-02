<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
check_session();

$query = "SELECT * FROM logs ORDER BY event_time DESC";
$logs = execute_query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Logs</title>
    <link rel="stylesheet" href="/oikia/assets/css/styles.css?v=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .back-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
        }

        .back-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <h2>Logs de Eventos</h2>
    <table>
        <tr>
            <th>Tipo de Evento</th>
            <th>Usuario</th>
            <th>Descripción</th>
            <th>Hora del Evento</th>
        </tr>
        <?php while ($log = $logs->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($log['event_type']); ?></td>
                <td><?php echo htmlspecialchars($log['username']); ?></td>
                <td><?php echo htmlspecialchars($log['description']); ?></td>
                <td><?php echo htmlspecialchars($log['event_time']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <a href="index.php" class="back-button">Regresar al Menú</a>
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
