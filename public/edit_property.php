<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

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
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
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
        <button type="submit">Agregar Propiedad</button>
        <?php include 'back_button.php'; ?>
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
</body>
</html>
