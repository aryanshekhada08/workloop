<?php
session_start();
require("../db.php");

// Check client login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$client_id = $_SESSION['user_id'];
$order_id = intval($_POST['order_id'] ?? 0);
$payment_method = $_POST['payment_method'] ?? 'dummy';

if ($order_id <= 0) {
    $_SESSION['error'] = "Invalid order ID.";
    header("Location: orders.php");
    exit();
}

// Verify order belongs to client and is payable (pending or delivered)
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

if (!in_array($order['status'], ['pending', 'delivered'])) {
    $_SESSION['error'] = "Order cannot be paid in current status.";
    header("Location: orders.php");
    exit();
}

// Simulate payment processing (always success in dummy)

header("Location: orders.php");
exit();
