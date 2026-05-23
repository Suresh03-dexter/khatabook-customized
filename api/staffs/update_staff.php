<?php 
require_once '../../config/db.php'; 
header('Content-Type: application/json');  

$staffId = $_POST['staffId'] ?? '';
$subadminId = $_POST['subadminId'] ?? '';
$staffRole = $_POST['staffRole'] ?? '';
$staffMobile = $_POST['staffMobile'] ?? '';
$staffAddress = $_POST['staffAddress'] ?? '';

if ($staffId && $subadminId && $staffRole && $staffMobile) {
    // Get the subadmin's name
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ? AND TRIM(LOWER(role)) = 'subadmin'");
    $stmt->bind_param("i", $subadminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($subadminRow = $result->fetch_assoc()) {
        $subadminName = $subadminRow['username'];
        
        $stmt = $conn->prepare("UPDATE staffs SET staff_name = ?, subadmin_id = ?, staff_role = ?, staff_mobile = ?, staff_address = ? WHERE id = ?");
        $stmt->bind_param("sisssi", $subadminName, $subadminId, $staffRole, $staffMobile, $staffAddress, $staffId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Staff updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update staff']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Selected subadmin not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
}
?>