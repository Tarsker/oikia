<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
check_session();
$user = get_logged_in_user();

// Recuperar la lista de propiedades desde la base de datos
$query = "SELECT id, type, description, address FROM properties";
$properties = execute_query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Propiedades</title>
    <link rel="stylesheet" href="/oikia/assets/css/styles.css?v=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
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
    <div class="container">
        <h2>Lista de Propiedades</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Dirección</th>
            </tr>
            <?php while ($property = $properties->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($property['id']); ?></td>
                    <td><?php echo htmlspecialchars($property['type']); ?></td>
                    <td><?php echo htmlspecialchars($property['description']); ?></td>
                    <td><?php echo htmlspecialchars($property['address']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="index.php" class="back-button">Regresar al Menú</a>
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
