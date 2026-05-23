<?php
// Database configuration
$host = "localhost";
$user = "root";           // Default XAMPP user
$password = "";           // Default XAMPP has no password
$dbname = "khatabook_db"; // Your database name

// --- PDO Connection ---
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password);

    // Set PDO error mode to Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Default fetch mode
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}

// --- MySQLi Connection ---
$conn = new mysqli($host, $user, $password, $dbname);

// Check mysqli connection
if ($conn->connect_error) {
    die("MySQLi Connection failed: " . $conn->connect_error);
}

// Set charset for mysqli connection
$conn->set_charset("utf8mb4");
?>
