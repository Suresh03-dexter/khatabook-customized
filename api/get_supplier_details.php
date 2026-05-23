<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';
session_start();

// Set header BEFORE any echo
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$supplier_id = intval($_GET['id']);

// ✅ Use prepared statements without user_id
$stmt = $conn->prepare("SELECT id, supplier_name, mobile, address, opening_balance, current_balance, product_type, status, created_at FROM suppliers WHERE id = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result && $result->num_rows === 1) {
    echo json_encode($result->fetch_assoc());
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Supplier not found']);
}
?>
