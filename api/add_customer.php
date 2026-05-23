<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';

header('Content-Type: application/json');

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$mobile = trim($data['mobile'] ?? '');
$balance = floatval($data['balance'] ?? 0);

if ($name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Customer name is required']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO customers (user_id, name, mobile, balance, type, status, created_at) 
                        VALUES (?, ?, ?, ?, 'customer', 1, NOW())");
$stmt->bind_param("issd", $user_id, $name, $mobile, $balance);

if ($stmt->execute()) {
    $newId = $conn->inster_id;
    logModification($conn, "Customer Entry #$newId - Added");
    echo json_encode(['status' => 'success', 'message' => 'Customer added successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
