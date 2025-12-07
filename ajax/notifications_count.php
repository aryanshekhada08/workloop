<?php
require '../db.php';
session_start();
$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    echo json_encode(['count' => 0]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id=? AND is_read=0");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode(['count' => (int)($result['total'] ?? 0)]);
?>
