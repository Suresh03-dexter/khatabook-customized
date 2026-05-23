<?php
session_start();
require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

// Sanitize and validate inputs
$customer_id = isset($data['customer_id']) ? intval($data['customer_id']) : 0;
$customer = isset($data['customer_name']) ? trim($data['customer_name']) : '';
$customer_type = isset($data['customer_type']) ? trim($data['customer_type']) : ''; // ✅ added
$amount = isset($data['amount']) ? floatval($data['amount']) : 0;
$pending = isset($data['pending_amount']) ? floatval($data['pending_amount']) : 0;
$status = isset($data['status']) ? $data['status'] : '';

// Validate
if (
    $customer_id <= 0 || 
    empty($customer) || 
    $amount <= 0 || 
    empty($customer_type) ||  // ✅ added
    !in_array($status, ['paid', 'pending'])
) {
  echo json_encode(['success' => false, 'message' => 'Invalid input']);
  exit;
}

// Optional: Check if customer_id exists for this user (data integrity)
$check = $conn->prepare("SELECT id FROM customers WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $customer_id, $user_id);
$check->execute();
$check_result = $check->get_result();
if ($check_result->num_rows === 0) {
  echo json_encode(['success' => false, 'message' => 'Customer not found']);
  exit;
}

// Insert into purchases
$stmt = $conn->prepare("
    INSERT INTO purchases 
        (user_id, customer_id, customer_name, customer_type, amount, pending_amount, status) 
    VALUES 
        (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("iissdds", $user_id, $customer_id, $customer, $customer_type, $amount, $pending, $status);

if ($stmt->execute()) {
  $newId = $conn->instert_id;
    logModification($conn, "Purchase Entry #$newId - Added");
  echo json_encode(['success' => true, 'message' => 'Purchase added successfully', 'purchase_id => $newId']);
} else {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}
?>
