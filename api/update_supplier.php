<?php
require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data['id'] ?? 0);
$name = trim($data['supplier_name'] ?? '');
$mobile = trim($data['mobile'] ?? '');
$product = trim($data['product_type'] ?? '');
$address = trim($data['address'] ?? '');
$opening = floatval($data['opening_balance'] ?? 0);   // You Will Give
$current = floatval($data['current_balance'] ?? 0);   // You Will Get

if ($id <= 0 || $name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid supplier data.']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE suppliers 
    SET supplier_name=?, 
        mobile=?, 
        product_type=?, 
        address=?, 
        opening_balance=?, 
        current_balance=? 
    WHERE id=?
");
$stmt->bind_param("ssssddi", $name, $mobile, $product, $address, $opening, $current, $id);

if ($stmt->execute()) {
    logModification($conn, "Supplier Entry #$id - Updated");
    echo json_encode(['status' => 'success', 'message' => 'Supplier updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update supplier.']);
}
?>
