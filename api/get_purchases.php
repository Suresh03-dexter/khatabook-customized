<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  echo json_encode([]);
  exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            p.id, 
            p.customer_id, 
            p.customer_name, 
            p.customer_type,  -- ✅ Added
            p.amount, 
            p.pending_amount, 
            p.status,
            c.mobile
        FROM purchases p
        JOIN customers c ON p.customer_id = c.id
        WHERE p.user_id = ?
        ORDER BY p.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$purchases = [];
while ($row = $result->fetch_assoc()) {
  $purchases[] = $row;
}

echo json_encode($purchases);
?>
