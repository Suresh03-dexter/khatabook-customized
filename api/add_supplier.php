<?php
require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';

session_start();

header('Content-Type: application/json');

// Read JSON body
$data = json_decode(file_get_contents("php://input"), true);

// Extract and sanitize inputs
$name    = trim($data['supplier_name'] ?? '');
$mobile  = trim($data['mobile'] ?? '');
$product = trim($data['product_type'] ?? '');
$address = trim($data['address'] ?? '');

// ✅ Convert to numeric properly even if empty string or null
$opening = isset($data['opening_balance']) && $data['opening_balance'] !== '' 
    ? floatval($data['opening_balance']) 
    : 0;

$current = isset($data['current_balance']) && $data['current_balance'] !== '' 
    ? floatval($data['current_balance']) 
    : 0;

// ✅ Validation
if ($name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Supplier name is required.']);
    exit;
}

// ✅ Prepare insert statement
$stmt = $conn->prepare("
    INSERT INTO suppliers 
    (supplier_name, mobile, product_type, address, opening_balance, current_balance, status) 
    VALUES (?, ?, ?, ?, ?, ?, 'active')
");

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    exit;
}

// ✅ Bind parameters safely
$stmt->bind_param("ssssdd", $name, $mobile, $product, $address, $opening, $current);

// ✅ Execute insert
if ($stmt->execute()) {
    $newId = $conn->instert_id;
    logModification($conn, "Supplier Entry #$newId - Added");
    echo json_encode(['status' => 'success', 'message' => 'Supplier added successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add supplier: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
