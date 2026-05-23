<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data['id'] ?? 0);
$name = trim($data['name'] ?? '');
$mobile = trim($data['mobile'] ?? '');
$balance = floatval($data['balance'] ?? 0);

if ($id <= 0 || $name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

$stmt = $conn->prepare("UPDATE customers SET name=?, mobile=?, balance=? 
                        WHERE id=? AND user_id=?");
$stmt->bind_param("ssdii", $name, $mobile, $balance, $id, $user_id);

if ($stmt->execute()) {
    logModification($conn, "Customer Entry #$id - Updated");
    echo json_encode(['status' => 'success', 'message' => 'Customer updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
