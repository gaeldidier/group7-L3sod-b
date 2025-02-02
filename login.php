<?php
session_start();
require 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verify user credentials
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        header("Location: chat.php");
    } else {
        echo "Invalid username or password.";
    }
}
?>

<!-- HTML Form for login -->
<div class="login-container">
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <input type="text" name="username" class="input-field" placeholder="Username" required>
        <input type="password" name="password" class="input-field" placeholder="Password" required>
        <button type="submit" class="login-button">Login</button>
    </form>
    <div class="signup-link">
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>
</div>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7fc;
        margin: 0;
        padding: 0;
    }

    .login-container {
        width: 350px;
        margin: 100px auto;
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .login-container h2 {
        font-size: 24px;
        color: #4CAF50;
        margin-bottom: 20px;
    }

    .input-field {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
    }

    .input-field:focus {
        border-color: #4CAF50;
        outline: none;
    }

    .login-button {
        width: 100%;
        padding: 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .login-button:hover {
        background-color: #45a049;
    }

    .signup-link {
        margin-top: 15px;
        font-size: 14px;
    }

    .signup-link a {
        text-decoration: none;
        color: #4CAF50;
    }

    .signup-link a:hover {
        text-decoration: underline;
    }
</style>
