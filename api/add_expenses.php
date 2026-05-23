<?php
session_start();

// 🔒 Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized - User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id']; // ✅ This must NOT be null

require_once '../config/db.php';
require_once __DIR__ . '/tracker/log_tracker.php';


// Validate required fields
$required = ['date', 'category', 'description', 'amount'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => "$field is required"]);
        exit;
    }
}

try {
    $date = $_POST['date'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = floatval($_POST['amount']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO expenses (user_id, date, category, description, amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssd", $user_id, $date, $category, $description, $amount);

    if ($stmt->execute()) {
        $newId = $conn->inster_id;
        logModification($conn, "Expense Entry #$newId - Added");
        echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
    } else {
        throw new Exception("Database error: " . $stmt->error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>