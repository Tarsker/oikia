<?php
require_once '../core/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];

    // Verificar si el token es válido
    $query = "SELECT * FROM users WHERE verification_code = ?";
    $result = execute_query($query, [$token]);

    if ($result->num_rows > 0) {
        // Restablecer la contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ?, verification_code = NULL WHERE verification_code = ?";
        execute_query($query, [$hashed_password, $token]);

        $success_message = "Tu contraseña ha sido restablecida exitosamente.";
    } else {
        $error_message = "El enlace de restablecimiento de contraseña no es válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="/oikia/assets/css/styles.css?v=1">
    <style>
        body {
            background-color: #f4f4f4;
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
            background: #4caf50; /* Botón color verde */
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-buttons button:hover {
            background: #45a049;
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
    <form action="reset_password.php" method="post">
        <h2>Restablecer Contraseña</h2>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php elseif (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
        <label for="new_password">Nueva Contraseña:</label>
        <input type="password" id="new_password" name="new_password" required>
        <div class="form-buttons">
            <button type="submit">Restablecer</button>
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
