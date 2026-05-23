<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Calculate sums
$sql = "
    SELECT
        SUM(CASE WHEN balance < 0 THEN balance ELSE 0 END) AS total_give,
        SUM(CASE WHEN balance > 0 THEN balance ELSE 0 END) AS total_get
    FROM customers
    WHERE user_id = ? AND type = 'customer' AND status = 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
    exit();
}

$result = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

// Convert negative give to positive for display
$you_will_give = abs($result['total_give'] ?? 0);
$you_will_get  = $result['total_get'] ?? 0;

echo json_encode([
    'you_will_give' => $you_will_give,
    'you_will_get'  => $you_will_get
]);
?>
