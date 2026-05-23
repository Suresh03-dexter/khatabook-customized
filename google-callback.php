<?php

session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db.php'; // Assumes PDO instance $pdo is available

/**
 * Load .env manually
 */

$envFile = __DIR__ . '/.env';

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {

    // Ignore comments
    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    list($key, $value) = explode('=', $line, 2);

    $_ENV[trim($key)] = trim($value);
}


/**
 * Google OAuth
 */

$client = new Google_Client();

$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->addScope("email");
$client->addScope("profile");

try {
    // ✅ STEP 1: Validate Authorization Code
    if (!isset($_GET['code'])) {
        throw new Exception("No authorization code received.");
    }

    // ✅ STEP 2: Exchange auth code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // ✅ STEP 3: Check for token errors
    if (isset($token['error'])) {
        throw new Exception("Google Login Error: " . $token['error']);
    }

    $client->setAccessToken($token['access_token']);

    // ✅ STEP 4: Retrieve user info from Google
    $oauth = new Google_Service_Oauth2($client);
    $userData = $oauth->userinfo->get();

    $email = filter_var($userData->email, FILTER_SANITIZE_EMAIL);
    $name = htmlspecialchars($userData->name, ENT_QUOTES, 'UTF-8');

    // ✅ STEP 5: Check if user exists in DB
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (! $user) {
        ?>
        <script>
        alert("you are not in database, please contact admin");
        window.location.href = "http://localhost/khatabook/index.php";
        </script>
        <?php
        exit();
        //$role = 'subadmin';

        // $adminCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        // $adminCheck->execute();
        // $isFirstAdmin = $adminCheck->fetchColumn() == 0;

        // if ($isFirstAdmin) {
        //     $role = 'admin';
        // }

        // Insert user
        // $insert = $pdo->prepare("INSERT INTO users (username, email, role) VALUES (?, ?, ?)");
        // $insert->execute([$name, $email, $role]);
    }

    // ✅ STEP 6: Start session and fetch role again
    $_SESSION['username'] = $email;
    $_SESSION['name'] = $name;

    $stmt = $pdo->prepare("SELECT role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    $_SESSION['role'] = $user['role'] ?? 'subadmin'; // fallback role
    $_SESSION['user_id'] = $row['id']; // Assuming you have user ID in the session

    // ✅ STEP 7: Redirect to main page
    // Fetch user from database
$check_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");

if ($user = mysqli_fetch_assoc($check_user)) {
    $_SESSION['username'] = $user['email'];
    $_SESSION['name'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['user_id'] = $user['id']; 
    header("Location: http://localhost/khatabook/pages/bussiness.php");
    exit();

} else {
    // If user not found, redirect to login page
    header("Location: http://localhost/khatabook/index.php");
    exit();
}

} catch (Exception $e) {
    // Handle errors
    error_log("Google Login Error: " . $e->getMessage());
    echo "An error occurred during Google login. Please try again later.";
}