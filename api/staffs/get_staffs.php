<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/db.php';
header('Content-Type: application/json');

// Fetch all staff with subadmin name
$sql = "SELECT s.id, s.staff_name, s.staff_role, s.staff_mobile, s.staff_address, IFNULL(DATE_FORMAT(s.joined_at, '%d-%m-%Y %h:%i %p')
, 'N/A') AS date_added,
               u.username AS subadmin_name
        FROM staffs s
        LEFT JOIN users u ON s.subadmin_id = u.id
        ORDER BY s.joined_at DESC";

$result = $conn->query($sql);   

$staffs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $staffs[] = $row;
    }
}

echo json_encode($staffs);
