<?php
session_start();
require_once '../config/db.php';
require_once '../config/csrf.php';

header('Content-Type: application/json');

// CSRF check
if (!validateToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit();
}

// Only admin allowed
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Inputs
$id = intval($_POST['id']);
$username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$role = $_POST['role'];

if (!$id || !$username || !$email || !in_array($role, ['admin', 'subadmin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $email, $role, $id]);

    // Log
    $log = $pdo->prepare("INSERT INTO user_actions (admin_email, action, target_email) VALUES (?, ?, ?)");
    $log->execute([$_SESSION['username'], 'edit', $email]);

    echo json_encode(['status' => 'success', 'message' => 'User updated']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}
