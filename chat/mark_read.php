<?php
require("../db.php");
$data = json_decode(file_get_contents('php://input'), true);
$sender = $data['sender_id'];
$receiver = $data['receiver_id'];

$stmt = $conn->prepare("UPDATE messages SET is_read=1 WHERE sender_id=? AND receiver_id=?");
$stmt->bind_param("ii", $sender, $receiver);
$stmt->execute();
