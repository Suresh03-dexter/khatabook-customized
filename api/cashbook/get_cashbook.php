<?php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode([]);
  exit;
}

$user_id = $_SESSION['user_id'];
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = "WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($from && $to) {
  $where .= " AND entry_date BETWEEN ? AND ?";
  $params[] = $from;
  $params[] = $to;
  $types .= "ss";
}

$sql = "SELECT * FROM cashbook $where ORDER BY entry_date ASC, entry_time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

echo json_encode($data);
?>
