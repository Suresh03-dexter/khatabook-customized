<?php
session_start();
require_once '../config/db.php'; // Check this path

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;

/* -------------------------
   1. Weekly & Monthly Sales
-------------------------- */
$weeklySales = $conn->query("
    SELECT IFNULL(SUM(amount),0) AS total 
    FROM purchases
    WHERE user_id='$user_id' 
      AND WEEK(created_at)=WEEK(CURDATE()) 
      AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

$monthlySales = $conn->query("
    SELECT IFNULL(SUM(amount),0) AS total 
    FROM purchases
    WHERE user_id='$user_id' 
      AND MONTH(created_at)=MONTH(CURDATE()) 
      AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

/* -------------------------
   2. Weekly & Monthly Expenses
-------------------------- */
$weeklyExpenses = $conn->query("
    SELECT IFNULL(SUM(amount),0) AS total 
    FROM expenses
    WHERE user_id='$user_id' 
      AND WEEK(created_at)=WEEK(CURDATE()) 
      AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

$monthlyExpenses = $conn->query("
    SELECT IFNULL(SUM(amount),0) AS total 
    FROM expenses
    WHERE user_id='$user_id' 
      AND MONTH(created_at)=MONTH(CURDATE()) 
      AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

/* -------------------------
   3. Profit Calculation
-------------------------- */
$weeklyProfit = $weeklySales - $weeklyExpenses;
$monthlyProfit = $monthlySales - $monthlyExpenses;

/* -------------------------
   4. Customer Counts
-------------------------- */
$peopleCount = $conn->query("
    SELECT COUNT(DISTINCT customer_id) AS cnt 
    FROM purchases 
    WHERE user_id='$user_id' AND customer_type='people'
")->fetch_assoc()['cnt'];

$hotelCount = $conn->query("
    SELECT COUNT(DISTINCT customer_id) AS cnt 
    FROM purchases 
    WHERE user_id='$user_id' AND customer_type='hotel'
")->fetch_assoc()['cnt'];

/* -------------------------
   5. Return JSON Response
-------------------------- */
echo json_encode([
    'weeklyProfit' => $weeklyProfit,
    'monthlyProfit' => $monthlyProfit,
    'weeklyExpenses' => $weeklyExpenses,
    'monthlyExpenses' => $monthlyExpenses,
    'peopleCount' => $peopleCount,
    'hotelCount' => $hotelCount
]);
?>
