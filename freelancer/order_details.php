<?php
session_start();
require("../db.php");

// Load Composer autoloader for mPDF
require_once __DIR__ . '/../vendor/autoload.php';
use Mpdf\Mpdf;

// ✅ User authentication and role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['id'] ?? 0);

if ($order_id <= 0) {
    die("Invalid Order ID");
}

// ✅ Fetch order details ensuring client owns the order
$stmt = $conn->prepare("
    SELECT o.*, g.title AS gig_title, g.price, g.delivery_time, g.freelancer_id,
           f.name AS freelancer_name, f.email AS freelancer_email,
           c.name AS client_name, c.email AS client_email
    FROM orders o
    JOIN gigs g ON o.gig_id = g.id
    JOIN users f ON g.freelancer_id = f.id
    JOIN users c ON o.client_id = c.id
    WHERE o.id = ? AND o.client_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found or unauthorized.");
}

// ✅ Invoice meta
$invoiceNumber = "INV-" . date("Ymd") . "-" . str_pad($order_id, 5, "0", STR_PAD_LEFT);
$invoiceDate   = date("d M, Y");
$dueDate       = date("d M, Y", strtotime("+14 days"));
$qty = 1;
$rate = floatval($order['price']);
$total = $qty * $rate;

// ✅ Check if invoice already exists in DB
$invoiceCheck = $conn->prepare("SELECT * FROM invoices WHERE order_id = ? LIMIT 1");
$invoiceCheck->bind_param("i", $order_id);
$invoiceCheck->execute();
$existingInvoice = $invoiceCheck->get_result()->fetch_assoc();
$invoiceCheck->close();

// ✅ If no invoice, insert into DB
if (!$existingInvoice) {
    $insertInvoice = $conn->prepare("
        INSERT INTO invoices (order_id, client_id, freelancer_id, invoice_number, amount) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $insertInvoice->bind_param("iiisd", $order_id, $user_id, $order['freelancer_id'], $invoiceNumber, $total);
    $insertInvoice->execute();
    $insertInvoice->close();
} else {
    // Reuse existing invoice number
    $invoiceNumber = $existingInvoice['invoice_number'];
}

// ✅ CSS styling
$css = "
body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #111; }
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
.logo { width: 90px; }
.brand { font-size: 18px; font-weight: bold; letter-spacing: 2px; }
.invoice-title { font-size: 26px; font-weight: bold; text-transform: uppercase; }
.meta { text-align: right; font-size: 12px; color: #555; }
.section-title { font-size: 10px; letter-spacing: 1px; color: #666; text-transform: uppercase; margin: 20px 0 6px; }
.block { border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #fff; font-size: 12px; }
.table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.table th { background: #f5f5f5; text-align: left; padding: 10px; font-size: 11px; border-bottom: 1px solid #ddd; text-transform: uppercase; }
.table td { padding: 10px; border-bottom: 1px solid #eee; }
.table .num { text-align: right; }
.totals { margin-top: 20px; width: 40%; float: right; border-collapse: collapse; }
.totals td { padding: 6px; }
.totals .label { color: #666; }
.totals .grand { font-weight: bold; font-size: 14px; border-top: 2px solid #111; padding-top: 8px; }
.footer { clear: both; margin-top: 40px; font-size: 11px; color: #555; border-top: 1px solid #ddd; padding-top: 10px; text-align:center; }
.badge { background: #111; color: #fff; padding: 4px 8px; border-radius: 12px; font-size: 10px; }
.thankyou { font-size: 14px; margin-top: 30px; font-weight: bold; }
";

// ✅ Invoice HTML
$html = '
<div class="header">
  <div>
    <img src="../assets/logo.png" class="logo"><br>
    <div class="small">Freelance Marketplace</div>
  </div>
  <div class="meta">
    <div class="invoice-title">INVOICE</div>
    <div>Invoice #: ' . $invoiceNumber . '</div>
    <div>Date: ' . $invoiceDate . '</div>
    <div>Due: ' . $dueDate . '</div>
  </div>
</div>

<table width="100%" cellspacing="0" cellpadding="6">
  <tr>
    <td width="50%" valign="top">
      <div class="section-title">Billed To (Freelancer)</div>
      <div class="block">
        <strong>' . htmlspecialchars($order["freelancer_name"]) . '</strong><br>
        ' . htmlspecialchars($order["freelancer_email"]) . '
      </div>
    </td>
    <td width="50%" valign="top">
      <div class="section-title">From (Client)</div>
      <div class="block">
        <strong>' . htmlspecialchars($order["client_name"]) . '</strong><br>
        ' . htmlspecialchars($order["client_email"]) . '
      </div>
    </td>
  </tr>
</table>

<div class="section-title">Order Details</div>
<table class="table">
  <tr>
    <th>Description</th>
    <th class="num">Rate</th>
    <th class="num">Qty</th>
    <th class="num">Total</th>
  </tr>
  <tr>
    <td>' . htmlspecialchars($order["gig_title"]) . '</td>
    <td class="num">₹' . number_format($rate, 2) . '</td>
    <td class="num">' . $qty . '</td>
    <td class="num">₹' . number_format($total, 2) . '</td>
  </tr>
</table>

<table class="totals">
  <tr>
    <td class="label">Subtotal</td>
    <td class="num">₹' . number_format($total, 2) . '</td>
  </tr>
  <tr>
    <td class="grand">Total Due</td>
    <td class="num grand">₹' . number_format($total, 2) . '</td>
  </tr>
</table>

<div class="footer">
  <p class="thankyou">Thank you for using Workloop! We appreciate your business.</p>
  <p>Status: <span class="badge">Completed</span></p>
</div>
';

// ✅ Ensure /invoices directory exists and set file path
$invoiceDir = __DIR__ . '/../invoices/';
if (!is_dir($invoiceDir)) {
    mkdir($invoiceDir, 0777, true);
}
$filename = "Invoice_{$invoiceNumber}.pdf";
$filePath = $invoiceDir . $filename;

// ✅ Generate styled PDF and save it to /invoices
if (ob_get_length()) ob_end_clean();
$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
$mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
$mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

// ✅ Offer file for immediate download to client
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filePath);
exit;
?>
