<?php
session_start();
require 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insert user into database
    $query = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$username, $password])) {
        $_SESSION['username'] = $username;
        header("Location: chat.php");
    } else {
        echo "Error: Could not register the user.";
    }
}
?>

<!-- HTML Form for registration -->
<div class="signup-container">
    <h2>Create Account</h2>
    <form method="POST" action="register.php">
        <input type="text" name="username" class="input-field" placeholder="Username" required>
        <input type="email" name="email" class="input-field" placeholder="Email" required>
        <input type="password" name="password" class="input-field" placeholder="Password" required>
        <input type="password" name="confirm_password" class="input-field" placeholder="Confirm Password" required>
        <button type="submit" class="signup-button">Sign Up</button>
    </form>
    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7fc;
        margin: 0;
        padding: 0;
    }

    .signup-container {
        width: 350px;
        margin: 100px auto;
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .signup-container h2 {
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

    .signup-button {
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

    .signup-button:hover {
        background-color: #45a049;
    }

    .login-link {
        margin-top: 15px;
        font-size: 14px;
    }

    .login-link a {
        text-decoration: none;
        color: #4CAF50;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
</style>
