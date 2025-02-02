<?php
session_start();

if (isset($_POST['sender'], $_POST['receiver'], $_POST['message'])) {
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver']; // For now, you can hardcode the receiver or get it dynamically.
    $message = $_POST['message'];

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chat_app";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the message into the database
    $stmt = $conn->prepare("INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $sender, $receiver, $message);
    $stmt->execute();

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>
