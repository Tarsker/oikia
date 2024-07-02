<?php
require_once '../core/functions.php';

$success_message = null;
$error_message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    rate_limit($ip_address, 'register');

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validar email y contraseña
    if (!validate_email($email)) {
        $error_message = "Correo no válido. Por favor, intenta nuevamente.";
    } elseif (!validate_password($password)) {
        $error_message = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        // Registrar el usuario
        if (register_user($name, $email, $password)) {
            $success_message = "Registro exitoso. Por favor, espera a que el administrador apruebe tu solicitud.";
        } else {
            $error_message = "Ocurrió un error al registrar el usuario. Por favor, intenta nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrarse</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=1">
    <style>
        body {
            background-color: #f0e68c; /* Fondo color khaki */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
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
        form input[type="email"],
        form input[type="password"] {
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
            background: #ffa500; /* Botón color naranja */
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-buttons button:hover {
            background: #ff8c00;
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

        .error, .success {
            color: red;
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <form action="register.php" method="post">
            <h2>Registrarse</h2>
            <?php if ($error_message): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php elseif ($success_message): ?>
                <div class="success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required><br>
            <label for="email">Correo:</label>
            <input type="email" id="email" name="email" required><br>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br>
            <div class="form-buttons">
                <button type="submit">Registrarse</button>
                <a href="login.php" class="back-button">Regresar al Login</a>
            </div>
        </form>
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
