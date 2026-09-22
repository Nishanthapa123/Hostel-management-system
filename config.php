<?php
session_start();

$host = getenv('HOSTEL_DB_HOST') ?: 'localhost';
$user = getenv('HOSTEL_DB_USER') ?: 'root';
$pass = getenv('HOSTEL_DB_PASS') ?: '';
$dbname = getenv('HOSTEL_DB_NAME') ?: 'hostel_management';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$baseUrl = getenv('HOSTEL_BASE_URL') ?: '/HostelManagementSystem/';
define('BASE_URL', rtrim($baseUrl, '/') . '/');

function redirect($path) {
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

function flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlash() {
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function formatCurrency($amount) {
    return '₹' . number_format((float)$amount, 2);
}

function safeValue($value) {
    global $conn;
    return $conn->real_escape_string(trim($value));
}

function getAdminName() {
    if (!isset($_SESSION['admin_id'])) {
        return 'Admin';
    }

    global $conn;
    $stmt = $conn->prepare('SELECT full_name FROM admins WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['admin_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    return $admin ? $admin['full_name'] : 'Admin';
}
