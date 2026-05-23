<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// 🛡️ Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Optional filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

// ✅ Base query
$sql = "SELECT supplier_name, product_type, mobile, address, opening_balance, current_balance, created_at FROM suppliers WHERE 1=1";

// ✅ Parameters for binding
$params = [];
$types = '';

// Filter: search by name
if (!empty($search)) {
    $sql .= " AND supplier_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

// Filter: date range
if (!empty($from) && !empty($to)) {
    $sql .= " AND DATE(created_at) BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
    $types .= "ss";
}

// ✅ Final ordering
$sql .= " ORDER BY created_at DESC";

// ✅ Prepare the statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare SQL statement.']);
    exit;
}

// ✅ Bind params if any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// ✅ Execute and fetch data
$stmt->execute();
$result = $stmt->get_result();

$suppliers = [];
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}

// ✅ Send as JSON
echo json_encode(['suppliers' => $suppliers]);
?>
