<?php 
require_once '../../config/db.php'; 
header('Content-Type: application/json');  

$staffId = $_GET['id'] ?? '';

if ($staffId) {
    $stmt = $conn->prepare("SELECT id, staff_name, subadmin_id, staff_role, staff_mobile, staff_address FROM staffs WHERE id = ?");
    $stmt->bind_param("i", $staffId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Staff not found']);
    }
} else {
    echo json_encode(['error' => 'Staff ID required']);
}
?>