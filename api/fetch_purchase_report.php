<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'User not authenticated']);
  exit;
}

$user_id = $_SESSION['user_id'];
$response = [
  'total_purchase' => 0,
  'total_pending' => 0,
  'paid_count' => 0,
  'pending_count' => 0,
  'top_customer' => 'N/A',
  'last_purchase' => 'N/A'
];

try {
  // 1. Totals
  $stmt = $conn->prepare("SELECT SUM(amount), SUM(pending_amount) FROM purchases WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($total_purchase, $total_pending);
  if ($stmt->fetch()) {
    $response['total_purchase'] = $total_purchase ?? 0;
    $response['total_pending'] = $total_pending ?? 0;
  }
  $stmt->close();

  // 2. Counts
  $stmt = $conn->prepare("SELECT status, COUNT(*) FROM purchases WHERE user_id = ? GROUP BY status");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($status, $count);
  while ($stmt->fetch()) {
    if ($status === 'paid') $response['paid_count'] = $count;
    if ($status === 'pending') $response['pending_count'] = $count;
  }
  $stmt->close();

  // 3. Top customer
  $stmt = $conn->prepare("SELECT customer_name, COUNT(*) as c FROM purchases WHERE user_id = ? GROUP BY customer_name ORDER BY c DESC LIMIT 1");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($top_customer, $top_count);
  if ($stmt->fetch()) {
    $response['top_customer'] = $top_customer;
  }
  $stmt->close();

  // 4. Last purchase
  $stmt = $conn->prepare("SELECT created_at FROM purchases WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($created_at);
  if ($stmt->fetch()) {
    $response['last_purchase'] = date('d M Y, h:i A', strtotime($created_at));
  }
  $stmt->close();

  echo json_encode($response);

} catch (Exception $e) {
  file_put_contents('report_debug.log', date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . "\n", FILE_APPEND);
  echo json_encode(['error' => 'Internal server error']);
}
