<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config/db.php';
require_once __DIR__ . '/../tracker/log_tracker.php';

session_start();

header('Content-Type: application/json');

// 🛡️ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Read raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

// ✅ Validate input
if (
    !isset($data['type']) || 
    !isset($data['amount']) || 
    !isset($data['entry_date']) || 
    !is_numeric($data['amount']) || 
    empty(trim($data['type'])) || 
    empty(trim($data['entry_date']))
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing fields']);
    exit;
}

// ✅ Sanitize input
$type = $conn->real_escape_string($data['type']);
$amount = (float) $data['amount'];
$note = isset($data['note']) ? $conn->real_escape_string($data['note']) : '';
$entry_date = $conn->real_escape_string($data['entry_date']);
$entry_time = date('H:i:s'); // 🕒 Save server time

// ✅ Prepare insert query
$stmt = $conn->prepare("INSERT INTO cashbook (user_id, type, amount, note, entry_date, entry_time) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isdsss", $user_id, $type, $amount, $note, $entry_date, $entry_time);

if ($stmt->execute()) {
    logModification($conn, "Cashbook Entry #$id - Added");
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}
?>
