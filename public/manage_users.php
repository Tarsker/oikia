<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

check_session();
$user = get_logged_in_user();

if ($user['role'] !== 'admin') {
    die("Acceso denegado.");
}

// Obtener todos los usuarios
$query = "SELECT id, username, email, role FROM users";
$users = execute_query($query)->fetch_all(MYSQLI_ASSOC);

// Procesar la actualización de roles
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];

    $query = "UPDATE users SET role = ? WHERE id = ?";
    execute_query($query, [$role, $user_id]);

    // Recargar la página para mostrar los cambios
    header("Location: manage_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios</title>
    <link rel="stylesheet" href="/oikia/assets/css/styles.css?v=1">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Gestionar Usuarios</h2>
        <?php if ($users): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre de Usuario</th>
                        <th>Correo Electrónico</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <form action="manage_users.php" method="post">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role">
                                        <option value="user" <?php if ($user['role'] === 'user') echo 'selected'; ?>>User</option>
                                        <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                                    </select>
                                    <button type="submit">Actualizar Rol</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay usuarios registrados.</p>
        <?php endif; ?>
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
