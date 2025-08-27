<?php
require("../db.php");
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];

$res = $conn->query("SELECT sender_id, COUNT(*) as cnt FROM messages WHERE receiver_id=$user_id AND is_read=0 GROUP BY sender_id");
$out = [];
while($row = $res->fetch_assoc()) {
    $out[$row['sender_id']] = $row['cnt'];
}
header('Content-Type: application/json');
echo json_encode($out);
