<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

check_session();
$user = get_logged_in_user();

// Parámetros de paginación
$default_limit = 50; // Límite predeterminado
$limits = [5, 10, 15, 20, 50, 100]; // Opciones de registros por página
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $limits) ? (int)$_GET['limit'] : $default_limit;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Obtener filtros si los hay
$property_id = $_GET['property_id'] ?? null;
$month = $_GET['month'] ?? null;

// Obtener los registros de gastos con paginación
$expenses = get_expenses($property_id, $month, $limit, $offset);

// Obtener el total de registros para la paginación
$total_expenses = get_expenses_count($property_id, $month);
$total_pages = ceil($total_expenses / $limit);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Gastos</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
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

        .filter-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .filter-form input, .filter-form select {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .filter-form button {
            padding: 10px 15px;
            background: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .filter-form button:hover {
            background: #4cae4c;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Lista de Gastos</h2>
        <form class="filter-form" method="GET" action="view_expenses.php">
            <input type="text" name="name" placeholder="Nombre del Gasto" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
            <input type="text" name="person" placeholder="Persona" value="<?php echo isset($_GET['person']) ? htmlspecialchars($_GET['person']) : ''; ?>">
            <input type="month" name="month" value="<?php echo isset($_GET['month']) ? htmlspecialchars($_GET['month']) : ''; ?>">
            <select name="property_id">
                <option value="">Todas las Propiedades</option>
                <?php
                $properties_query = "SELECT id, type, description, address FROM properties";
                $properties_result = execute_query($properties_query);
                while ($property = $properties_result->fetch_assoc()): ?>
                    <option value="<?php echo $property['id']; ?>" <?php echo isset($_GET['property_id']) && $_GET['property_id'] == $property['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($property['type'] . " - " . $property['description'] . " - " . $property['address']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label for="limit">Registros por página:</label>
            <select id="limit" name="limit" onchange="changeLimit()">
                <?php foreach ([5, 10, 15, 20, 50, 100] as $option): ?>
                    <option value="<?= $option ?>" <?= $option == $limit ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filtrar</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID del Gasto</th>
                    <th>Nombre</th>
                    <th>Monto</th>
                    <th>Persona</th>
                    <th>Mes</th>
                    <th>Propiedad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($expense = $expenses->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($expense['id']); ?></td>
                        <td><?php echo htmlspecialchars($expense['name']); ?></td>
                        <td><?php echo htmlspecialchars($expense['value']); ?></td>
                        <td><?php echo htmlspecialchars($expense['person']); ?></td>
                        <td><?php echo htmlspecialchars($expense['month']); ?></td>
                        <td><?php echo htmlspecialchars($expense['type'] . " - " . $expense['description'] . " - " . $expense['address']); ?></td>
                        <td>
                            <a href="edit_expense.php?id=<?php echo $expense['id']; ?>">Editar</a>
                            <a href="delete_expense.php?id=<?php echo $expense['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este gasto?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&property_id=<?= $property_id ?>&month=<?= $month ?>">Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&limit=<?= $limit ?>&property_id=<?= $property_id ?>&month=<?= $month ?>" <?= $i == $page ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&property_id=<?= $property_id ?>&month=<?= $month ?>">Siguiente</a>
            <?php endif; ?>
        </div>
        <a href="index.php" class="back-button">Regresar al Menú</a>
    </div>
    <?php include 'footer.php'; ?>
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        document.onkeydown = function(e) {
            if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === "I") || (e.ctrlKey && e.key === "U")) {
                e.preventDefault();
                return false;
            }
        };

        function changeLimit() {
            const limit = document.getElementById('limit').value;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('limit', limit);
            urlParams.set('page', 1); // Volver a la primera página
            window.location.search = urlParams.toString();
        }
    </script>
</body>
</html>
