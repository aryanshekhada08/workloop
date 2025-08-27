<?php
session_start();
require("../db.php");

// Check client login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$client_id = $_SESSION['user_id'];

if ($order_id <= 0) {
    $_SESSION['error'] = "Invalid order ID.";
    header("Location: orders.php");
    exit();
}

// Verify order belongs to client and is cancellable (pending)
$stmt = $conn->prepare("SELECT status FROM orders WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $order_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Order not found or access denied.";
    header("Location: orders.php");
    exit();
}

$order = $result->fetch_assoc();

if ($order['status'] !== 'pending') {
    $_SESSION['error'] = "Only pending orders can be cancelled.";
    header("Location: orders.php");
    exit();
}

// Update status to cancelled and clear deadline
$updateStmt = $conn->prepare("UPDATE orders SET status = 'cancelled', deadline = NULL WHERE id = ? AND client_id = ?");
$updateStmt->bind_param("ii", $order_id, $client_id);

if ($updateStmt->execute()) {
    $_SESSION['success'] = "Order cancelled successfully.";
} else {
    $_SESSION['error'] = "Failed to cancel order. Please try again.";
}

header("Location: orders.php");
exit();
?>