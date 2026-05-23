<?php
require_once '../../config/db.php';
require_once __DIR__ . '/../tracker/log_tracker.php';

session_start();

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

$user_id = $_SESSION['user_id'];
$id = intval($_POST['id'] ?? 0);
$type = $_POST['type'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);
$note = trim($_POST['note'] ?? '');
$date = $_POST['date'] ?? '';

if (!in_array($type, ['in', 'out']) || $amount <= 0 || !$date || $id <= 0) {
  echo json_encode(['error' => 'Invalid data']);
  exit;
}

$stmt = $conn->prepare("UPDATE cashbook SET type = ?, amount = ?, note = ?, entry_date = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sdssii", $type, $amount, $note, $date, $id, $user_id);
$success = $stmt->execute();
logModification($conn, "Cashbook Entry #$id - Updated");
echo json_encode(['success' => $success]);
?>
