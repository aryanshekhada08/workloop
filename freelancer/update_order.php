<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$freelancer_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = $_GET['action'] ?? '';

if ($order_id <= 0 || !in_array($action, ['accept', 'reject'])) {
    $_SESSION['error'] = "Invalid order or action.";
    header("Location: orders.php");
    exit();
}

// Map action to status string
$status = '';
$deadline = null;
if ($action === 'accept') {
    // Fetch delivery_time from gigs table for the order's gig
    $gigQuery = $conn->prepare("SELECT g.delivery_time FROM gigs g JOIN orders o ON o.gig_id = g.id WHERE o.id = ?");
    $gigQuery->bind_param("i", $order_id);
    $gigQuery->execute();
    $gigResult = $gigQuery->get_result();
    $gig = $gigResult->fetch_assoc();

    $delivery_time = intval($gig['delivery_time'] ?? 7); // Use delivery_time or default 7 days

    $status = 'active';  // Status when order accepted

    // Set deadline = today + delivery_time days
    $deadline = date('Y-m-d', strtotime("+$delivery_time days"));
} elseif ($action === 'reject') {
    $status = 'cancelled';

    // For cancelled orders, set deadline as the order's created_at date (order placement date)
    $orderDateQuery = $conn->prepare("SELECT updated_at FROM orders WHERE id = ?");
    $orderDateQuery->bind_param("i", $order_id);
    $orderDateQuery->execute();
    $orderDateResult = $orderDateQuery->get_result();
    $orderRow = $orderDateResult->fetch_assoc();
    $deadline = isset($orderRow['updated_at']) ? date('Y-m-d', strtotime($orderRow['updated_at'])) : null;
}

// Then your update query updating status and deadline accordingly:
if ($deadline !== null) {
    $query = "UPDATE orders SET status = ?, deadline = ? WHERE id = ? AND freelancer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $status, $deadline, $order_id, $freelancer_id);
} else {
    $query = "UPDATE orders SET status = ? WHERE id = ? AND freelancer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $status, $order_id, $freelancer_id);
}

if ($stmt->execute()) {
    $_SESSION['success'] = "Order " . htmlspecialchars($action) . "ed successfully.";
} else {
    $_SESSION['error'] = "Failed to update order: " . $stmt->error;
}

header("Location: orders.php");
exit();


?>
