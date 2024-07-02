<?php
require_once '../core/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Verificar si el correo existe en la base de datos
    $query = "SELECT * FROM users WHERE email = ?";
    $result = execute_query($query, [$email]);

    if ($result->num_rows > 0) {
        // Generar un token único para la recuperación de contraseña
        $token = bin2hex(random_bytes(50));

        // Guardar el token en el campo verification_code
        $query = "UPDATE users SET verification_code = ? WHERE email = ?";
        execute_query($query, [$token, $email]);

        // Enviar el correo de restablecimiento de contraseña
        $reset_link = "http://localhost/oikia/public/reset_password.php?token=$token";
        $subject = "Recuperar Contraseña - Oikia";
        $body = "Para restablecer tu contraseña, haz clic en el siguiente enlace: <a href='$reset_link'>$reset_link</a>";

        if (send_email($email, $subject, $body)) {
            $success_message = "Se ha enviado un correo para restablecer tu contraseña.";
        } else {
            $error_message = "Ocurrió un error al enviar el correo. Por favor, intenta nuevamente.";
        }
    } else {
        $error_message = "No se encontró una cuenta con ese correo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="/oikia/assets/css/styles.css?v=1">
    <style>
        body {
            background-color: #ffebcd; /* Fondo color blanchedalmond */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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

        form input[type="email"] {
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
            background: #ff6347; /* Botón color tomato */
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-buttons button:hover {
            background: #ee5a36;
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
    <form action="forgot_password.php" method="post">
        <h2>Recuperar Contraseña</h2>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php elseif (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <label for="email">Correo:</label>
        <input type="email" id="email" name="email" required>
        <div class="form-buttons">
            <button type="submit">Enviar</button>
            <a href="login.php" class="back-button">Regresar al Login</a>
        </div>
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
