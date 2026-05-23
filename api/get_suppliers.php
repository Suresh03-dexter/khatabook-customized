<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM suppliers WHERE status = 'active'";

// 🔍 Apply search
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (supplier_name LIKE '%$search%' OR mobile LIKE '%$search%')";
}

// 🧠 Apply sorting
switch ($filter) {
    case 'highest':
        $sql .= " ORDER BY current_balance DESC";
        break;
    case 'least':
        $sql .= " ORDER BY current_balance ASC";
        break;
    case 'az':
        $sql .= " ORDER BY supplier_name ASC";
        break;
    case 'oldest':
        $sql .= " ORDER BY created_at ASC";
        break;
    case 'recent':
        $sql .= " ORDER BY created_at DESC";
        break;
    case 'you_give':
        $sql .= " ORDER BY current_balance ASC"; // assuming give = negative balance
        break;
    case 'you_get':
        $sql .= " ORDER BY current_balance DESC"; // assuming get = positive balance
        break;
    default:
        $sql .= " ORDER BY created_at DESC";
        break;
}

// 📄 Add pagination
$sql .= " LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$suppliers = [];
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}

// 🔹 Calculate total for hasMore
$countSql = "SELECT COUNT(*) AS total FROM suppliers WHERE status = 'active'";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $countSql .= " AND (supplier_name LIKE '%$search%' OR mobile LIKE '%$search%')";
}
$total = $conn->query($countSql)->fetch_assoc()['total'];

$hasMore = $page * $limit < $total;

echo json_encode([
    "suppliers" => $suppliers,
    "hasMore" => $hasMore
]);
?>
