<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($customer_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid customer ID']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT item_name, quantity, amount, created_at 
                            FROM customer_items 
                            WHERE customer_id = ? 
                            ORDER BY created_at DESC");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    $total_amount = 0;
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        $total_amount += (float)$row['amount'];
    }

    echo json_encode([
        'success' => true,
        'data' => $items,
        'total_amount' => $total_amount
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
