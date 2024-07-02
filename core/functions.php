<?php
require_once 'db.php';
require_once '../vendor/autoload.php'; // Asegúrate de que la ruta a autoload.php es correcta

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Función para proteger entrada de datos
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Función para registrar usuarios
function register_user($name, $email, $password) {
    $name = clean_input($name);
    $email = clean_input($email);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password, is_verified) VALUES (?, ?, ?, 0)";
    return execute_query($query, [$name, $email, $hashed_password]);
}

// Función para verificar usuario
function verify_user($email, $password) {
    $email = clean_input($email);
    $query = "SELECT * FROM users WHERE email = ?";
    $result = execute_query($query, [$email]);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            if ($row['is_verified'] == 0) {
                return false; // Usuario no verificado
            }
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role']; // Asegúrate de que el rol se establece en la sesión
            return true;
        }
    }
    return false;
}

// Función para verificar sesión de usuario
function check_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Función para obtener usuario logueado
function get_logged_in_user() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['user_name'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'] // Asegúrate de que el rol se recupera de la sesión
        ];
    }
    return null;
}

// Función para formatear valores monetarios
function format_currency($value) {
    return '$' . number_format($value, 2, ',', '.');
}

// Función para limitar la tasa de solicitudes (rate limiting)
function rate_limit($user_id, $action, $limit = 5, $interval = '15 MINUTE') {
    global $conn;

    $query = "SELECT COUNT(*) as request_count
              FROM request_logs
              WHERE user_id = ? AND action = ? AND timestamp > (NOW() - INTERVAL $interval)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $user_id, $action);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['request_count'] >= $limit) {
        die("Rate limit exceeded. Please try again later.");
    }

    $query = "INSERT INTO request_logs (user_id, action) VALUES (?, ?)";
    $stmt->bind_param('is', $user_id, $action);
    $stmt->execute();
}

// Función para enviar correos electrónicos
function send_email($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = 'a046ba9fc33581'; // Sustituye con tu username de Mailtrap
        $mail->Password = '4e1c7edb0beb07'; // Sustituye con tu password de Mailtrap
        $mail->SMTPSecure = 'tls';
        $mail->Port = 2525;

        // Remitente
        $mail->setFrom('no-reply@oikia.com', 'Oikia');
        $mail->addAddress($to);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Función para registrar eventos
function log_event($event_type, $username, $description) {
    global $conn;

    $query = "INSERT INTO logs (event_type, username, description, event_time) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $event_type, $username, $description);
    $stmt->execute();
    $stmt->close();
}

// Función para aprobar usuarios
function approve_user($user_id) {
    $query = "UPDATE users SET is_verified = 1 WHERE id = ?";
    execute_query($query, [$user_id]);
    $username = execute_query("SELECT username FROM users WHERE id = ?", [$user_id])->fetch_assoc()['username'];
    log_event('approve_user', 'admin', "User $username approved");
}

// Función para obtener gastos con filtros y paginación
function get_expenses($property_id = null, $month = null, $limit = 50, $offset = 0) {
    global $conn;

    $query = "SELECT e.id, e.name, e.value, e.person, e.month, p.type, p.description, p.address 
              FROM expenses e 
              JOIN properties p ON e.property_id = p.id 
              WHERE 1=1";

    $params = [];
    $types = '';

    if ($property_id) {
        $query .= " AND e.property_id = ?";
        $params[] = $property_id;
        $types .= 'i';
    }

    if ($month) {
        $query .= " AND e.month = ?";
        $params[] = $month;
        $types .= 's';
    }

    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

// Función para contar el total de gastos con filtros
function get_expenses_count($property_id = null, $month = null) {
    global $conn;

    $query = "SELECT COUNT(*) as total 
              FROM expenses e 
              JOIN properties p ON e.property_id = p.id 
              WHERE 1=1";

    $params = [];
    $types = '';

    if ($property_id) {
        $query .= " AND e.property_id = ?";
        $params[] = $property_id;
        $types .= 'i';
    }

    if ($month) {
        $query .= " AND e.month = ?";
        $params[] = $month;
        $types .= 's';
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total'];
}
?>
