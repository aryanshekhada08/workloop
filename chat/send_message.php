<?php
require("../db.php");

// Validate and sanitize inputs
$sender = isset($_POST['sender_id']) ? intval($_POST['sender_id']) : 0;
$receiver = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($sender > 0 && $receiver > 0 && !empty($message)) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, is_read, sent_at) VALUES (?, ?, ?, 0, NOW())");
    if ($stmt === false) {
        http_response_code(500);
        echo "Database prepare failed.";
        exit();
    }
    $stmt->bind_param("iis", $sender, $receiver, $message);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        echo "Failed to send message.";
        exit();
    }

    $stmt->close();
    echo "Message sent successfully";
} else {
    http_response_code(400);
    echo "Invalid input";
}
$conn->close();
?>
