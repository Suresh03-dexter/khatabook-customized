
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PKhatabook - Login</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f2f2f2;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background-color: white;
      padding: 30px;
      border-radius: 20px;
      width: 90%;
      max-width: 360px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      text-align: center;
    }

    .login-container img {
      width: 100px;
      margin-bottom: 20px;
    }

    .login-container h2 {
      margin-bottom: 10px;
      color: #333;
    }

    .login-container p {
      font-size: 14px;
      color: #777;
      margin-bottom: 20px;
    }

    .gmail-btn {
      background-color: #DB4437;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      width: 100%;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .gmail-btn:hover {
      background-color: #c33b2e;
    }

    .divider {
      margin: 20px 0;
      font-size: 13px;
      color: #aaa;
    }

    .footer {
      font-size: 12px;
      color: #888;
      margin-top: 15px;
    }

  </style>
</head>
<body>
  <div class="login-container">
    <img src="https://img.icons8.com/color/96/000000/google-logo.png" alt="Google Logo" />
    <h2>Welcome to PKhatabook</h2>
    <p>Please login with your Google account</p>

   <a href="google-login.php">
  <button type="button" class="gmail-btn">Login with Gmail</button>
</a>

    <p class="footer">By continuing, you agree to our Terms of Service & Privacy Policy.</p>
  </div>
</body>
</html>
