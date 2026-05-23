<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid supplier ID.']);
    exit;
}

// Option 1: Soft delete (recommended)
// $sql = "UPDATE suppliers SET status='inactive' WHERE id=$id";

// Option 2: Hard delete (permanent)
$sql = "DELETE FROM suppliers WHERE id=$id";

if ($conn->query($sql)) {
    if (function_exists('logModification')) {
        logModification($conn, "Supplier Entry #$id Deleted");
    }
    echo json_encode(['status' => 'success', 'message' => 'Supplier deleted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete supplier.']);
}
