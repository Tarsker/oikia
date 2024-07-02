<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
check_session();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM expenses WHERE id = ?";
    execute_query($query, [$id]);

    header("Location: view_expenses.php");
    exit;
}
?>
