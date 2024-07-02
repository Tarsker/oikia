<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];
    $query = "SELECT id FROM users WHERE verification_code = ?";
    $result = execute_query($query, [$verification_code]);

    if ($result->num_rows > 0) {
        $query = "UPDATE users SET is_verified = 1, verification_code = NULL WHERE verification_code = ?";
        execute_query($query, [$verification_code]);
        echo "Email verificado con éxito.";
    } else {
        echo "Código de verificación inválido.";
    }
}
?>
