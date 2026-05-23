<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode($result ?: []);
