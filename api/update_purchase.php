<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id']);
$customer = trim($data['customer_name']);
$customer_type = isset($data['customer_type']) ? trim($data['customer_type']) : ''; // ✅ added
$amount = floatval($data['amount']);
$pending = floatval($data['pending_amount']);
$status = $data['status'];
$user_id = $_SESSION['user_id'];

if ($id <= 0 || $customer === '' || $amount <= 0 || empty($customer_type) || !in_array($status, ['paid', 'pending'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid input']);
  exit;
}

$stmt = $conn->prepare("
    UPDATE purchases 
    SET customer_name=?, customer_type=?, amount=?, pending_amount=?, status=? 
    WHERE id=? AND user_id=?
");
$stmt->bind_param("ssddsii", $customer, $customer_type, $amount, $pending, $status, $id, $user_id);

if ($stmt->execute()) {
  logModification($conn, "Purchase Entry #$id - Updated");
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'DB update error']);
}
?>
