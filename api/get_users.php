<?php

// Start session and set JSON header
session_start();
header('Content-Type: application/json');

// Ensure only logged-in admins can access
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// Include DB connection
require_once("../config/db.php"); // Adjust this if your path is different

// Prepare and execute query
$sql = "SELECT id, username, email, role FROM users WHERE role = 'subadmin' ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            "id" => $row['id'],
            "name" => $row['username'], // Maps to `name` on frontend
            "email" => $row['email'],
            "role" => $row['role']
        ];
    }

    echo json_encode([
        "status" => "success",
        "users" => $users
    ]);
} else {
    echo json_encode([
        "status" => "success",
        "users" => []
    ]);
}

$conn->close();
?>
