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

// ✅ Convert date if needed for opening balance logic
if (!empty($from)) {
    $selected_date = date('Y-m-d', strtotime($from));
} else {
    $selected_date = date('Y-m-d'); // default to today if no date given
}

$where = "WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($from && $to) {
  $where .= " AND entry_date BETWEEN ? AND ?";
  $params[] = $from;
  $params[] = $to;
  $types .= "ss";
}

$summary = [
  'cash_in' => 0,
  'cash_out' => 0,
  'opening_balance' => 0,
  'closing_balance' => 0,
];

// ✅ Get cash in/out totals
$stmt = $conn->prepare("SELECT type, SUM(amount) as total FROM cashbook $where GROUP BY type");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  if ($row['type'] == 'in') $summary['cash_in'] = floatval($row['total']);
  if ($row['type'] == 'out') $summary['cash_out'] = floatval($row['total']);
}

// ✅ Get opening balance before selected date
$openingStmt = $conn->prepare("
  SELECT 
    SUM(CASE WHEN type = 'in' THEN amount ELSE -amount END) AS balance
  FROM cashbook
  WHERE user_id = ? AND entry_date < ?
");
$openingStmt->bind_param("is", $user_id, $selected_date);
$openingStmt->execute();
$openingResult = $openingStmt->get_result();
if ($openingRow = $openingResult->fetch_assoc()) {
    $summary['opening_balance'] = floatval($openingRow['balance']);
}

// Calculate closing balance
$summary['closing_balance'] = $summary['opening_balance'] + $summary['cash_in'] - $summary['cash_out'];

echo json_encode($summary);
?>
