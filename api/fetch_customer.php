<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Optional: Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_like = "%$search%";

// ✅ Optional: Filter dropdown
$filter = $_GET['filter'] ?? '';

// ✅ Base SQL
$sql = "SELECT id, name, mobile, balance, created_at 
        FROM customers 
        WHERE user_id = ? 
          AND type = 'customer' 
          AND status = 1";

// ✅ Search condition
$params = [];
$types = "i"; // for user_id
$params[] = $user_id;

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR mobile LIKE ?)";
    $types .= "ss";
    $params[] = $search_like;
    $params[] = $search_like;
}

// ✅ Apply filter logic
switch ($filter) {
    case 'recent':
        $orderBy = "ORDER BY created_at DESC";
        break;
    case 'oldest':
        $orderBy = "ORDER BY created_at ASC";
        break;
    case 'highest':
        $orderBy = "ORDER BY balance DESC";
        break;
    case 'least':
        $orderBy = "ORDER BY balance ASC";
        break;
    case 'az':
        // ✅ Trim & lowercase for more natural A-Z
        $orderBy = "ORDER BY TRIM(LOWER(name)) ASC";
        break;
    case 'you_get':
        $sql .= " AND balance > 0"; 
        $orderBy = "ORDER BY balance DESC";
        break;
    case 'you_give':
        $sql .= " AND balance < 0";
        $orderBy = "ORDER BY balance ASC";
        break;
    default:
        $orderBy = "ORDER BY id DESC"; 
}

$sql .= " $orderBy";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
    exit();
}

$result = $stmt->get_result();
$customers = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

echo json_encode($customers);
?>
