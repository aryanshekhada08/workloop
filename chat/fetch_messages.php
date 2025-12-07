<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");
require("../db.php");

// Your code below...

error_reporting(0);
header("Content-Type: application/json");
require("../db.php");



$sender = isset($_POST['sender_id']) ? intval($_POST['sender_id']) : 0;
$receiver = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;

if ($sender <= 0 || $receiver <= 0) {
    http_response_code(400);
    echo json_encode([]);
    exit();
}

// Fetch messages between sender and receiver ordered by sent_at
$stmt = $conn->prepare("SELECT sender_id, message, sent_at FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC");
$stmt->bind_param("iiii", $sender, $receiver, $receiver, $sender);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    // Get sender name for initials display
    $senderStmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $senderStmt->bind_param("i", $row['sender_id']);
    $senderStmt->execute();
    $senderStmt->bind_result($sender_name);
    $senderStmt->fetch();
    $senderStmt->close();

    $messages[] = [
        "sender_id" => (int)$row['sender_id'],
        "message" => $row['message'],
        "sent_at" => $row['sent_at'],
        "sender_name" => $sender_name ?? "Unknown"
    ];
}

header("Content-Type: application/json");
echo json_encode($messages);

$stmt->close();
$conn->close();
?>
