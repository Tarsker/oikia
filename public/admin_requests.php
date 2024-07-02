<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

check_session();
$user = get_logged_in_user();

// Verificar si el usuario es admin
if ($user['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Procesar solicitudes de creación de usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $user_id = $_POST['user_id'];

    if ($action == 'approve') {
        if (approve_user($user_id)) {
            log_event('approve_user', $user['username'], "User with ID $user_id approved");
        }
    } elseif ($action == 'deny') {
        $query = "DELETE FROM users WHERE id = ?";
        execute_query($query, [$user_id]);
        log_event('deny_user', $user['username'], "User with ID $user_id denied");
    }

    header("Location: admin_requests.php");
    exit;
}

// Obtener solicitudes de creación de usuario pendientes
$query = "SELECT id, username, email FROM users WHERE is_verified = 0";
$requests = execute_query($query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes de Administración</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=1">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Solicitudes de Administración</h2>
        <?php if (count($requests) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['username']) ?></td>
                            <td><?= htmlspecialchars($request['email']) ?></td>
                            <td>
                                <form action="admin_requests.php" method="post" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $request['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit">Aprobar</button>
                                </form>
                                <form action="admin_requests.php" method="post" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $request['id'] ?>">
                                    <input type="hidden" name="action" value="deny">
                                    <button type="submit">Denegar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay solicitudes pendientes.</p>
        <?php endif; ?>
        <a href="index.php" class="btn">Volver</a>
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
