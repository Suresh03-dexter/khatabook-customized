<?php

session_start();
header("Content-Type: text/plain");

// Check if admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied";
    exit;
}

require_once '../config/db.php';
require_once '../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateToken($csrf_token)) {
        http_response_code(400);
        echo "Invalid CSRF token";
        exit;
    }

    if (empty($username) || empty($email) || empty($role)) {
        http_response_code(400);
        echo "Missing fields";
        exit;
    }

    // Sanitize
    $username = htmlspecialchars($username, ENT_QUOTES);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "Email already exists";
        exit;
    }
    $stmt->close();

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $role);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
