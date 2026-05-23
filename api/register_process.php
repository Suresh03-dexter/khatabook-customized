<?php
require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Limit subadmin count to 5
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'subadmin'");
    $stmt->execute();
    if ($role === 'subadmin' && $stmt->fetchColumn() >= 5) {
        die("Subadmin limit reached.");
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);
    header("Location: ../pages/index.php");
}
