<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$invoice_number = $_GET['invoice'] ?? '';

if (!$invoice_number) {
    die("Invoice not specified.");
}

// Fetch invoice with order and freelancer verification
$stmt = $conn->prepare("
    SELECT i.*, g.freelancer_id 
    FROM invoices i
    JOIN orders o ON i.order_id = o.id
    JOIN gigs g ON o.gig_id = g.id
    WHERE i.invoice_number = ?
    LIMIT 1
");
$stmt->bind_param("s", $invoice_number);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();
$stmt->close();

if (!$invoice || $invoice['freelancer_id'] != $user_id) {
    die("Unauthorized access.");
}

$filepath = __DIR__ . '/../invoices/Invoice_' . $invoice_number . '.pdf';

if (!file_exists($filepath)) {
    die("Invoice file not found.");
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Invoice_' . $invoice_number . '.pdf"');
readfile($filepath);
exit;
