<?php
require("../db.php");

$sender = $_POST['sender_id'];
$receiver = $_POST['receiver_id'];

$stmt = $conn->prepare("SELECT sender_id, message, sent_at FROM messages WHERE 
    (sender_id = ? AND receiver_id = ?) OR 
    (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC");
$stmt->bind_param("iiii", $sender, $receiver, $receiver, $sender);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $align = ($row['sender_id'] == $sender) ? "justify-end" : "justify-start";
    $bg = ($row['sender_id'] == $sender) ? "bg-blue-500 text-white" : "bg-gray-300";
    echo "<div class='flex $align'><div class='max-w-xs px-4 py-2 rounded $bg mb-2'>{$row['message']}</div></div>";
}
