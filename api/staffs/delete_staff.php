<?php 
require_once '../../config/db.php'; 
header('Content-Type: application/json');  

$staffId = $_POST['staffId'] ?? $_GET['id'] ?? '';

if ($staffId) {
    $stmt = $conn->prepare("DELETE FROM staffs WHERE id = ?");
    $stmt->bind_param("i", $staffId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Staff deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete staff']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Staff ID required']);
}
?>