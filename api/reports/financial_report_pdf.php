<?php
session_start();
require_once '../../config/db.php';

// Ensure only logged-in users can access
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

$response = [
    'success' => true,
    'data' => [
        'purchases' => [],
        'expenses' => [],
        'cashbook' => [],
        'customers' => [],
        'suppliers' => []
    ]
];

// ===== Purchases =====
$sql = "SELECT 
            SUM(total_amount) AS total_purchases,
            SUM(CASE WHEN status='pending' THEN total_amount ELSE 0 END) AS total_pending
        FROM purchases
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['data']['purchases'] = [
    'total_purchases' => (float)($result['total_purchases'] ?? 0),
    'total_pending' => (float)($result['total_pending'] ?? 0)
];
$stmt->close();

// ===== Expenses =====
$sql = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['data']['expenses'] = [
    'total_expenses' => (float)($result['total_expenses'] ?? 0)
];
$stmt->close();

// ===== Cashbook =====
$sql = "SELECT 
            SUM(CASE WHEN type='cash_in' THEN amount ELSE 0 END) AS total_cash_in,
            SUM(CASE WHEN type='cash_out' THEN amount ELSE 0 END) AS total_cash_out
        FROM cashbook
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['data']['cashbook'] = [
    'total_cash_in' => (float)($result['total_cash_in'] ?? 0),
    'total_cash_out' => (float)($result['total_cash_out'] ?? 0)
];
$stmt->close();

// ===== Customers =====
$sql = "SELECT 
            COUNT(*) AS total_customers,
            SUM(balance) AS total_customer_balance
        FROM customers
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['data']['customers'] = [
    'total_customers' => (int)($result['total_customers'] ?? 0),
    'total_customer_balance' => (float)($result['total_customer_balance'] ?? 0)
];
$stmt->close();

// ===== Suppliers =====
$sql = "SELECT 
            COUNT(*) AS total_suppliers,
            SUM(balance) AS total_supplier_balance
        FROM suppliers
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$response['data']['suppliers'] = [
    'total_suppliers' => (int)($result['total_suppliers'] ?? 0),
    'total_supplier_balance' => (float)($result['total_supplier_balance'] ?? 0)
];
$stmt->close();

// Output JSON
header('Content-Type: application/json');
echo json_encode($response);
