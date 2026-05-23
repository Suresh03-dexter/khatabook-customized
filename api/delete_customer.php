<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// ✅ Only admin can delete
if ($role !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit;
}

// ✅ Get raw JSON input or fallback to POST
$input = json_decode(file_get_contents("php://input"), true);
$customer_id = intval($input['id'] ?? ($_POST['id'] ?? 0));

if ($customer_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid customer ID']);
    exit;
}

// ✅ Start transaction
$conn->begin_transaction();

try {
    // 1️⃣ Delete purchases linked to this customer
    $stmt1 = $conn->prepare("DELETE FROM purchases WHERE customer_id = ? AND user_id = ?");
    $stmt1->bind_param("ii", $customer_id, $user_id);
    $stmt1->execute();
    $stmt1->close();

    // 2️⃣ Permanently delete the customer
    $stmt2 = $conn->prepare("DELETE FROM customers WHERE id = ? AND user_id = ?");
    $stmt2->bind_param("ii", $customer_id, $user_id);
    $stmt2->execute();

    if ($stmt2->affected_rows > 0) {
         if (function_exists('logModification')) {
        logModification($conn, "Supplier Entry #$id Deleted");
    }
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Customer and related purchases deleted successfully']);
    } else {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Customer not found or unauthorized']);
    }

    $stmt2->close();
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
