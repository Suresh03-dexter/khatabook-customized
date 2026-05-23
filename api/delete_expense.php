<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode(["status" => "error", "message" => "Expense ID is required"]);
        exit;
    }

    $expense_id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];

    // First check if the expense exists and belongs to the user
    $check_stmt = $conn->prepare("SELECT id FROM expenses WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $expense_id, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Expense not found or doesn't belong to you"]);
        exit;
    }
    $check_stmt->close();

    // Delete the expense
    $delete_stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $expense_id, $user_id);

    if ($delete_stmt->execute()) {
         if (function_exists('logModification')) {
        logModification($conn, "Supplier Entry #$id Deleted");
    }
        if ($delete_stmt->affected_rows > 0) {
            echo json_encode(["status" => "deleted"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No rows affected"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $delete_stmt->error]);
    }
    $delete_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
$conn->close();
?>