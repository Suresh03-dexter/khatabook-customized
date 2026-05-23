<?php 
require_once '../../config/db.php'; 
header('Content-Type: application/json');  

$subadminId = $_POST['subadminId'] ?? '';
$staffRole = $_POST['staffRole'] ?? '';
$staffMobile = $_POST['staffMobile'] ?? '';
$staffAddress = $_POST['staffAddress'] ?? '';

if ($subadminId && $staffRole && $staffMobile) {
    // Get the subadmin's name from users table
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ? AND TRIM(LOWER(role)) = 'subadmin'");
    $stmt->bind_param("i", $subadminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($subadminRow = $result->fetch_assoc()) {
        $subadminName = $subadminRow['username'];
        
        // Insert into staffs table
        $stmt = $conn->prepare("INSERT INTO staffs (staff_name, subadmin_id, staff_role, staff_mobile, staff_address, joined_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sisss", $subadminName, $subadminId, $staffRole, $staffMobile, $staffAddress);

        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Staff added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database insert failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Selected subadmin not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields (subadmin, role, mobile)']);
}
?>