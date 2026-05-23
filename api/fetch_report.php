<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/db.php'; // gives $conn (MySQLi)

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$user_id     = $_SESSION['user_id'];
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$from        = !empty($_GET['from']) ? $_GET['from'] : null;
$to          = !empty($_GET['to']) ? $_GET['to'] : null;

// ✅ JOIN customer_items with customers to filter by user_id
$sql = "SELECT 
            ci.id, 
            ci.customer_id, 
            c.name,  -- ✅ fetch name from customers table
            ci.item_name, 
            ci.quantity, 
            ci.amount, 
            ci.created_at
        FROM customer_items ci
        INNER JOIN customers c ON ci.customer_id = c.id
        WHERE c.user_id = ?";

$params = [$user_id];
$types  = "i";

if ($customer_id > 0) {
    $sql .= " AND ci.customer_id = ?";
    $params[] = $customer_id;
    $types   .= "i";
}

if ($from && $to) {
    $sql .= " AND DATE(ci.created_at) BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
    $types   .= "ss";
}

$sql .= " ORDER BY ci.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL error: " . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);

$stmt->close();
$conn->close();
