<?php
require_once '../core/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (verify_user($email, $password)) {
        header("Location: index.php");
        exit;
    } else {
        $error_message = "Correo o contraseña incorrectos, o cuenta no verificada.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión en Oikia</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
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
        }

        .form-buttons button,
        .form-buttons a {
            flex: 1;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            margin: 0 5px;
            cursor: pointer;
        }

        .form-buttons button {
            background: #28a745;
        }

        .form-buttons button:hover,
        .form-buttons a:hover {
            background: #0056b3;
        }

        .form-buttons button:hover {
            background: #218838;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <form action="login.php" method="post">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <label for="email">Correo:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <div class="form-buttons">
                <button type="submit">Iniciar Sesión</button>
                <a href="register.php">Crear Usuario</a>
                <a href="forgot_password.php">Recuperar Contraseña</a>
            </div>
        </form>
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
    </script>
</body>
</html>
