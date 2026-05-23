<?php
require_once '../../config/db.php'; // adjust if your db.php path is different
require_once __DIR__ . '/../tracker/log_tracker.php';

session_start();

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

$user_id = $_SESSION['user_id'];

// Get POST data from JavaScript fetch
$data = json_decode(file_get_contents("php://input"), true);

// Collect input fields
$entry_date = $data['entry_date'] ?? '';
$entry_time = $data['entry_time'] ?? '';
$type = $data['type'] ?? '';
$amount = $data['amount'] ?? 0;
$description = $data['description'] ?? '';

if (!$entry_date || !$entry_time || !$type || !$amount) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing required fields']);
  exit;
}

// Prepare & insert into database
$stmt = $conn->prepare("INSERT INTO cashbook (user_id, entry_date, entry_time, type, amount, description) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssds", $user_id, $entry_date, $entry_time, $type, $amount, $description);

if ($stmt->execute()) {
  logModification($conn, "Cashbook Entry #$id - Added.");
  echo json_encode(['success' => true]);
} else {
  http_response_code(500);
  echo json_encode(['error' => 'Database insert failed']);
}
?>
