<?php
session_start();
require_once '../config/db.php';  

header('Content-Type: application/json');

if (!isset($_GET['customer_id']) || !is_numeric($_GET['customer_id'])) {
    echo json_encode([]);
    exit;
}

$customer_id = intval($_GET['customer_id']);
$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode([]);
    exit;
}

// 1️⃣ Fetch customer details
$customerSql = "SELECT id, name, mobile, balance AS initial_amount 
                FROM customers 
                WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($customerSql);
$stmt->bind_param("ii", $customer_id, $user_id);
$stmt->execute();
$customerResult = $stmt->get_result();
$customer = $customerResult->fetch_assoc();

if (!$customer) {
    echo json_encode([]);
    exit;
}

// 2️⃣ Fetch purchase transactions
$purchaseSql = "SELECT id, amount AS purchase_amount, pending_amount, status, created_at 
                FROM purchases 
                WHERE customer_id = ? AND user_id = ?
                ORDER BY created_at DESC";
$stmt2 = $conn->prepare($purchaseSql);
$stmt2->bind_param("ii", $customer_id, $user_id);
$stmt2->execute();
$purchaseResult = $stmt2->get_result();

$transactions = [];

if ($purchaseResult->num_rows > 0) {
    while ($row = $purchaseResult->fetch_assoc()) {
        $transactions[] = [
            'name' => $customer['name'],
            'mobile' => $customer['mobile'],
            'initial_amount' => $customer['initial_amount'],
            'purchase_amount' => $row['purchase_amount'],
            'pending_amount' => $row['pending_amount'],
            'status' => $row['status'],
            'created_at' => $row['created_at']
        ];
    }
} else {
    // 3️⃣ If no purchases, still show initial amount row
    $transactions[] = [
        'name' => $customer['name'],
        'mobile' => $customer['mobile'],
        'initial_amount' => $customer['initial_amount'],
        'purchase_amount' => 0,
        'pending_amount' => 0,
        'status' => null,
        'created_at' => null
    ];
}

echo json_encode($transactions);
