<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

// Read JSON body
$data = json_decode(file_get_contents("php://input"), true);

$customerId = $data['customer_id'] ?? 0;
$itemName   = trim($data['item_name'] ?? '');
$quantity   = trim($data['quantity'] ?? '');
$amount     = $data['amount'] ?? 0;

// Validate input
if (!$customerId || $itemName === '' || $quantity === '' || !$amount) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

// Prepare insert query
$stmt = $conn->prepare("INSERT INTO customer_items (customer_id, item_name, quantity, amount) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $customerId, $itemName, $quantity, $amount);
//   i = int (customer_id)
//   s = string (item_name)
//   s = string (quantity) ✅ NOW TEXT
//   i = int (amount)

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "DB insert failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
