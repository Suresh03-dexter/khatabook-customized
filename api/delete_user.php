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

// Validate ID
$id = intval($_POST['id']);
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
    exit();
}

try {
    // Get user email before delete
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    // Log
    $log = $pdo->prepare("INSERT INTO user_actions (admin_email, action, target_email) VALUES (?, ?, ?)");
    $log->execute([$_SESSION['username'], 'delete', $user['email']]);

    echo json_encode(['status' => 'success', 'message' => 'User deleted']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}
