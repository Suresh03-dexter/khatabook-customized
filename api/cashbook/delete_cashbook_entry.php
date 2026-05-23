<?php
// delete_cashbook_entry.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once __DIR__ . '/../tracker/log_tracker.php';

session_start();

// Auth check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Role check - admin only
$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden — admin only']);
    exit;
}

// Read JSON body
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid id']);
    exit;
}

// Optional: verify the entry exists (and optionally log it before deletion)
$check = $conn->prepare("SELECT id FROM cashbook WHERE id = ?");
$check->bind_param('i', $id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Entry not found']);
    exit;
}

// Perform delete
$del = $conn->prepare("DELETE FROM cashbook WHERE id = ?");
$del->bind_param('i', $id);

if ($del->execute()) {
    logModification($conn, "Cashbook  Entry #$id - Deleted");
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $del->error]);
}
?>
