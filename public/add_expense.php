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

$properties_list = [];
while ($property = $properties->fetch_assoc()) {
    $properties_list[] = $property;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = $_POST['property_id'];
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $value = isset($_POST['value']) ? $_POST['value'] : 0;
    $person = isset($_POST['person']) ? $_POST['person'] : '';
    $month = isset($_POST['month']) ? $_POST['month'] : '';

    $name = clean_input($name);
    $person = clean_input($person);
    $month = clean_input($month);
    $value = floatval($value);

    // Validación de datos
    if (empty($property_id) || empty($name) || empty($value) || empty($person) || empty($month)) {
        $error_message = "Todos los campos son obligatorios.";
    } else {
        $query = "INSERT INTO expenses (property_id, name, value, person, month) VALUES (?, ?, ?, ?, ?)";
        if (execute_query($query, [$property_id, $name, $value, $person, $month])) {
            log_event('add_expense', $user['username'], "Added expense: $name with value $value for property ID $property_id by $person for month $month");
            $success_message = "Gasto agregado exitosamente para la propiedad ID $property_id.";
        } else {
            $error_message = "Ocurrió un error al agregar el gasto. Por favor, intenta nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Gasto</title>
    <link rel="stylesheet" href="/oikia/assets/css/styles.css?v=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        form {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form h2 {
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
        }

        form select,
        form input[type="text"],
        form input[type="number"],
        form input[type="month"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        form button {
            padding: 10px 15px;
            background: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background: #4cae4c;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }

        .success {
            color: green;
            margin-bottom: 20px;
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
    <form action="add_expense.php" method="post">
        <h2>Agregar Gasto</h2>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php elseif (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <label for="property_id">Propiedad:</label>
        <select id="property_id" name="property_id" required>
            <?php foreach ($properties_list as $property): ?>
                <option value="<?php echo $property['id']; ?>">
                    <?php
                    $property_info = htmlspecialchars($property['type']);
                    if (!empty($property['description'])) {
                        $property_info .= " - " . htmlspecialchars($property['description']);
                    }
                    if (!empty($property['address'])) {
                        $property_info .= " - " . htmlspecialchars($property['address']);
                    }
                    echo $property_info;
                    ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="name">Nombre del Gasto:</label>
        <input type="text" id="name" name="name" required>
        <label for="value">Monto:</label>
        <input type="number" id="value" name="value" required step="0.01">
        <label for="person">Persona:</label>
        <input type="text" id="person" name="person" required>
        <label for="month">Mes:</label>
        <input type="month" id="month" name="month" required>
        <button type="submit">Agregar Gasto</button>
        <a href="index.php" class="back-button">Regresar al Menú</a>
    </form>
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
