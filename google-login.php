<?php
require __DIR__ . '/vendor/autoload.php';


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


$client->setPrompt('select_account'); // forces account chooser every time
$client->setPrompt('consent'); // forces consent screen every time
$login_url = $client->createAuthUrl();


header("Location: " . $login_url);
exit();
// After successful authentication:
$user = mysqli_fetch_assoc($result); // You fetched this from DB
$_SESSION['username'] = $user['email'];
$_SESSION['name'] = $user['name'];
$_SESSION['role'] = $user['role'];
$_SESSION['user_id'] = $user['id'];
// Verify session is working
error_log('Login successful for user ID: ' . $_SESSION['user_id']);
