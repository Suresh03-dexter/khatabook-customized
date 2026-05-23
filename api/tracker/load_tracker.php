<?php
session_start();
require_once '../../config/db.php'; // your DB connection file

// Security: Only admin
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$sql = "SELECT id, record_name, modified_by, role, DATE_FORMAT(modified_at, '%Y-%m-%d %h:%i %p') as modified_at
        FROM modification_tracker
        ORDER BY modified_at DESC";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
