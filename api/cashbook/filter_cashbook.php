<?php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

if (!$from_date || !$to_date) {
    echo json_encode([]);
    exit;
}

$query = "SELECT entry_date, type, note, amount 
          FROM cashbook 
          WHERE user_id = ? AND entry_date BETWEEN ? AND ?
          ORDER BY entry_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $user_id, $from_date, $to_date);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);
