<?php
require("../db.php");

$sender = $_POST['sender_id'];
$receiver = $_POST['receiver_id'];
$message = trim($_POST['message']);

if (!empty($message)) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, is_read, sent_at) VALUES (?, ?, ?, 0, NOW())");
    $stmt->bind_param("iis", $sender, $receiver, $message);
    $stmt->execute();
}
