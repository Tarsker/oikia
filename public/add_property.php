<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

check_session();
$user = get_logged_in_user();

$property_types = ['Casa', 'Terreno', 'Finca', 'Edificio', 'Apartamento', 'Parcela', 'Otro'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $custom_type = $_POST['custom_type'] ?? '';
    $address = $_POST['address'];
    $price = $_POST['price'];

    // Si el tipo es "Otro", usar el tipo personalizado
    if ($type == 'Otro' && !empty($custom_type)) {
        $type = clean_input($custom_type);
    } else {
        $type = clean_input($type);
    }

    $address = clean_input($address);
    $price = floatval($price);

    $query = "INSERT INTO properties (type, address, price) VALUES (?, ?, ?)";
    if (execute_query($query, [$type, $address, $price])) {
        $success_message = "Propiedad creada exitosamente.";
        log_event('add_property', $user['username'], "Created property: $type at $address for $price");
    } else {
        $error_message = "Ocurrió un error al crear la propiedad. Por favor, intenta nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Propiedad</title>
    <link rel="stylesheet" href="/oikia/assets/css/styles.css?v=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .user-info {
            text-align: right;
            padding: 10px;
            background-color: #e9ecef;
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

        form input[type="text"],
        form input[type="number"],
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-buttons button {
            padding: 10px 15px;
            background: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-buttons button:hover {
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
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
        }

        .back-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="user-info">
        <strong>Usuario logueado:</strong> <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
    </div>
    <form action="add_property.php" method="post">
        <h2>Agregar Propiedad</h2>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php elseif (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <label for="type">Tipo de Propiedad:</label>
        <select id="type" name="type" required onchange="toggleCustomType(this.value)">
            <?php foreach ($property_types as $type): ?>
                <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
            <?php endforeach; ?>
        </select>
        <div id="customTypeDiv" style="display: none;">
            <label for="custom_type">Especificar otro tipo:</label>
            <input type="text" id="custom_type" name="custom_type">
        </div>
        <label for="address">Dirección:</label>
        <input type="text" id="address" name="address" required>
        <label for="price">Precio:</label>
        <input type="number" id="price" name="price" required step="0.01">
        <div class="form-buttons">
            <button type="submit">Agregar Propiedad</button>
            <a href="index.php" class="back-button">Regresar al Menú</a>
        </div>
    </form>

    <script>
        function toggleCustomType(value) {
            var customTypeDiv = document.getElementById('customTypeDiv');
            if (value === 'Otro') {
                customTypeDiv.style.display = 'block';
            } else {
                customTypeDiv.style.display = 'none';
            }
        }
    </script>
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
