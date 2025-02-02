<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chat_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch messages from the database
$sql = "SELECT sender , message, timestamp FROM messages WHERE receiver = '" . $_SESSION['username'] . "' OR sender = '" . $_SESSION['username'] . "' ORDER BY timestamp ASC";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        /* Styling goes here */
        /* Use previous styles from the previous code for chat box, input, button, etc. */
    </style>
</head>
<body>
<h1>Welcome to the chat, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

<button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>

<div id="chatBox" class="chat-box">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['sender'] === $_SESSION['username']) {
                // Display user's messages on the right
                echo "<div class='chat-message user'>" . htmlspecialchars($row['message']) . "</div>";
            } else {
                // Display receiver's (bot's or other user's) messages on the left
                echo "<div class='chat-message bot'>" . htmlspecialchars($row['message']) . "</div>";
            }
        }
    }
    ?>
</div>

<div class="input-container">
    <input id="messageInput" type="text" placeholder="Type a message" />
    <button onclick="sendMessage()">Send</button>
</div>

<script>
    let ws;
    let username = "<?php echo $_SESSION['username']; ?>";

    function connect() {
        ws = new WebSocket("ws://127.0.0.1:8000/ws/" + username);
        
        ws.onopen = () => {
            console.log("Connected to WebSocket");
        };

        ws.onmessage = (event) => {
            const message = event.data;
            const chatBox = document.getElementById("chatBox");

            // Insert message to chat box
            const messageDiv = document.createElement("div");
            messageDiv.classList.add("chat-message");
            if (message.includes(username)) {
                messageDiv.classList.add("user");
            } else {
                messageDiv.classList.add("bot");
            }
            messageDiv.innerText = message;
            chatBox.appendChild(messageDiv);

            chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the bottom
        };

        ws.onclose = () => {
            console.log("WebSocket connection closed.");
        };

        ws.onerror = (error) => {
            console.error("WebSocket Error: ", error);
        };
    }

    function sendMessage() {
        const message = document.getElementById("messageInput").value;
        if (ws && ws.readyState === WebSocket.OPEN && message.trim() !== "") {
            // Send message via WebSocket
            ws.send(message);

            // Insert the message in the chat box (on the right side for user)
            const chatBox = document.getElementById("chatBox");
            const messageDiv = document.createElement("div");
            messageDiv.classList.add("chat-message", "user");
            messageDiv.innerText = message;
            chatBox.appendChild(messageDiv);

            // Scroll to the bottom
            chatBox.scrollTop = chatBox.scrollHeight;

            // Send the message to the server and store it in the database
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "store_message.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("sender=" + username + "&receiver=receiver_name_here&message=" + message);

            document.getElementById("messageInput").value = ''; // Clear the input field
        }
    }

    window.onload = connect;
</script>
<style>
    /* Reset and general page styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f2f5;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        color: #4CAF50;
        font-size: 24px;
        margin-top: 20px;
    }

    /* Chatbox styling */
    .chat-box {
        width: 80%;
        height: 400px;
        margin: 20px auto;
        background-color: #ffffff;
        border-radius: 8px;
        padding: 20px;
        overflow-y: auto;
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Styling for individual chat messages */
    .chat-message {
        padding: 10px;
        border-radius: 5px;
        background-color: #f1f1f1;
        margin-bottom: 10px;
        max-width: 70%;
    }

    /* Style for messages sent by the user (on the right side) */
    .chat-message.user {
        background-color: #d1ffd6;
        margin-left: auto;
        text-align: right;
    }

    /* Style for messages received (on the left side) */
    .chat-message.bot {
        background-color: #f0f0f0;
        margin-left: 0;
    }

    /* Input and send button */
    .input-container {
        display: flex;
        justify-content: space-between;
        margin: 10px auto;
        width: 80%;
    }

    #messageInput {
        width: 80%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #45a049;
    }

    /* Logout button */
    .logout-button {
        display: block;
        margin: 20px auto;
        background-color: #ff3b30;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-align: center;
        width: 200px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .logout-button:hover {
        background-color: #e02a1f;
    }

    /* Styling for the scrollbar in chatbox */
    .chat-box::-webkit-scrollbar {
        width: 10px;
    }

    .chat-box::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 10px;
    }

    .chat-box::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }
</style>

</body>
</html>
