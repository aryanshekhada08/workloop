<?php
session_start();
require("../db.php");

// Check client login and role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$userId  = $_SESSION['user_id'];
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
    $_SESSION['error'] = "Invalid order ID.";
    header("Location: dashboard.php");
    exit();
}

// Fetch order details with ownership and status
$stmt = $conn->prepare("SELECT status, amount, freelancer_id FROM orders WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$orderResult = $stmt->get_result();

if ($orderResult->num_rows !== 1) {
    $_SESSION['error'] = "Order not found or access denied.";
    header("Location: dashboard.php");
    exit();
}

$order = $orderResult->fetch_assoc();

// ✅ Check if already completed
if ($order['status'] === 'completed') {
    $_SESSION['error'] = "This order has already been marked as completed.";
    header("Location: dashboard.php");
    exit();
}

if ($order['status'] !== 'delivered') {
    $_SESSION['error'] = "Only delivered orders can be confirmed.";
    header("Location: dashboard.php");
    exit();
}

// Calculate amounts
$platformFeePercent = 0.20;
$orderAmount        = floatval($order['amount']);
$platformFeeAmount  = round($orderAmount * $platformFeePercent, 2);
$netEarning         = round($orderAmount * (1 - $platformFeePercent), 2);
$freelancerId       = intval($order['freelancer_id']);

$conn->begin_transaction();

try {
    // ✅ Update orders table to completed
    $updateOrder = $conn->prepare("
        UPDATE orders 
        SET status = 'completed', updated_at = NOW(), platform_fee = ? 
        WHERE id = ? AND client_id = ? AND status = 'delivered'
    ");
    $updateOrder->bind_param("dii", $platformFeeAmount, $orderId, $userId);
    if (!$updateOrder->execute() || $updateOrder->affected_rows === 0) {
        throw new Exception("Failed to update order status.");
    }
    $updateOrder->close();

    // ✅ Update or insert freelancer wallet balance
    $walletCheck = $conn->prepare("SELECT balance FROM freelancer_wallets WHERE freelancer_id = ?");
    $walletCheck->bind_param("i", $freelancerId);
    $walletCheck->execute();
    $walletCheck->store_result();

    if ($walletCheck->num_rows > 0) {
        $walletCheck->close();
        $walletUpdate = $conn->prepare("UPDATE freelancer_wallets SET balance = balance + ? WHERE freelancer_id = ?");
        $walletUpdate->bind_param("di", $netEarning, $freelancerId);
        if (!$walletUpdate->execute()) {
            throw new Exception("Failed to update freelancer wallet.");
        }
        $walletUpdate->close();
    } else {
        $walletCheck->close();
        $walletInsert = $conn->prepare("INSERT INTO freelancer_wallets (freelancer_id, balance) VALUES (?, ?)");
        $walletInsert->bind_param("id", $freelancerId, $netEarning);
        if (!$walletInsert->execute()) {
            throw new Exception("Failed to create freelancer wallet.");
        }
        $walletInsert->close();
    }

    $conn->commit();
    $_SESSION['success'] = "Order confirmed as completed! Platform fee deducted, and freelancer wallet updated.";

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Transaction failed: " . $e->getMessage();
}

header("Location: dashboard.php");
exit();
?>
