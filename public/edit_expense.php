<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
check_session();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $value = $_POST['value'];
    $person = $_POST['person'];
    $month = $_POST['month'];

    $query = "UPDATE expenses SET name = ?, value = ?, person = ?, month = ? WHERE id = ?";
    $params = [$name, $value, $person, $month, $id];
    execute_query($query, $params);

    header("Location: view_expenses.php");
    exit;
}

$id = $_GET['id'];
$query = "SELECT * FROM expenses WHERE id = ?";
$expense = execute_query($query, [$id])->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Gasto</title>
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

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="number"],
        input[type="month"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
            background: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #4cae4c;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #337ab7;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .back-button:hover {
            background: #286090;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Editar Gasto</h2>
        <form action="edit_expense.php" method="post">
            <input type="hidden" name="id" value="<?= $expense['id'] ?>">
            <label for="name">Nombre del Gasto:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($expense['name']) ?>" required>
            <label for="value">Valor:</label>
            <input type="number" id="value" name="value" value="<?= htmlspecialchars($expense['value']) ?>" required>
            <label for="person">Persona:</label>
            <input type="text" id="person" name="person" value="<?= htmlspecialchars($expense['person']) ?>" required>
            <label for="month">Mes:</label>
            <input type="month" id="month" name="month" value="<?= htmlspecialchars($expense['month']) ?>" required>
            <button type="submit">Guardar Cambios</button>
        </form>
        <a href="view_expenses.php" class="back-button">Regresar</a>
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
