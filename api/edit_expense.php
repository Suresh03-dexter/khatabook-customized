<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required = ['id', 'date', 'category', 'description', 'amount'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(["status" => "error", "message" => "$field is required"]);
            exit;
        }
    }

    $expense_id = intval($_POST['id']);
    $date = $_POST['date'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = floatval($_POST['amount']);
    $user_id = $_SESSION['user_id'];

    // Debug log
    error_log("Updating expense: ID=$expense_id, User=$user_id, Amount=$amount");

    $stmt = $conn->prepare("UPDATE expenses SET date=?, category=?, description=?, amount=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sssdii", $date, $category, $description, $amount, $expense_id, $user_id);

    if ($stmt->execute()) {
        logModification($conn, "Expense Entry #$id - Updated");
        echo json_encode(["status" => "success"]);
    } else {
        error_log("Update failed: " . $stmt->error);
        echo json_encode(["status" => "error", "message" => "Database update failed"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
$conn->close();
?>