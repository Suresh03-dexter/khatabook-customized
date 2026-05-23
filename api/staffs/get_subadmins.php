<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$sql = "SELECT id, username FROM users WHERE TRIM(LOWER(role)) = 'subadmin'";
$result = $conn->query($sql);

$subadmins = [];
while ($row = $result->fetch_assoc()) {
    $subadmins[] = $row;
}

echo json_encode($subadmins);
