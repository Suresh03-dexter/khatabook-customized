<?php
require_once '../config/db.php';
session_start();

// Default response
$response = [
    'you_will_give' => 0,
    'you_will_get'  => 0
];

// ✅ Sum both balances directly based on new columns
$sql = "
    SELECT 
        IFNULL(SUM(opening_balance), 0) AS total_give,
        IFNULL(SUM(current_balance), 0) AS total_get
    FROM suppliers
    WHERE status = 'active'
";

$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $response['you_will_give'] = floatval($row['total_give']);
    $response['you_will_get']  = floatval($row['total_get']);
}

// ✅ Output JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
