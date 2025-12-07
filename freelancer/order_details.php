<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['id'] ?? 0);

if ($order_id <= 0) {
    die("Invalid Order ID");
}

// Fetch order details ensuring freelancer owns the gig
$stmt = $conn->prepare("
    SELECT o.*, g.title AS gig_title, g.price, g.delivery_time, g.freelancer_id,
           c.name AS client_name, c.email AS client_email
    FROM orders o
    JOIN gigs g ON o.gig_id = g.id
    JOIN users c ON o.client_id = c.id
    WHERE o.id = ? AND g.freelancer_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found or unauthorized.");
}

// Check if invoice exists for this order
$invoiceCheck = $conn->prepare("SELECT invoice_number FROM invoices WHERE order_id = ? LIMIT 1");
$invoiceCheck->bind_param("i", $order_id);
$invoiceCheck->execute();
$invoiceResult = $invoiceCheck->get_result();
$invoice = $invoiceResult->fetch_assoc();
$invoiceCheck->close();

$message = '';
$allowed_statuses = ['pending', 'active', 'delivered', 'cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? '';
    $delivery_message = trim($_POST['delivery_message'] ?? '');
    $delivery_file_name = $order['delivery_file'] ?? '';

    if (!in_array($new_status, $allowed_statuses)) {
        $message = "Invalid status.";
    } else {
        if (isset($_FILES['delivery_file']) && $_FILES['delivery_file']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['delivery_file']['size'] <= 10485760) { // max 10MB
                $uploadDir = "../uploads/deliveries/";
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
                $delivery_file_name = time() . "_" . basename($_FILES['delivery_file']['name']);
                if (!move_uploaded_file($_FILES['delivery_file']['tmp_name'], $uploadDir . $delivery_file_name)) {
                    $message = "Failed to save delivery file.";
                }
            } else {
                $message = "Delivery file must be under 10MB.";
            }
        }

        if (!$message) {
            $update = $conn->prepare("UPDATE orders SET status = ?, delivery_file = ?, delivery_message = ? WHERE id = ? AND freelancer_id = ?");
            if ($update === false) {
                $message = "Database error: unable to prepare statement.";
            } else {
                $update->bind_param("sssii", $new_status, $delivery_file_name, $delivery_message, $order_id, $user_id);
                if ($update->execute()) {
                    $message = "Order updated successfully.";
                    $order['status'] = $new_status;
                    $order['delivery_file'] = $delivery_file_name;
                    $order['delivery_message'] = $delivery_message;
                } else {
                    $message = "Failed to update order.";
                }
                $update->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Order Details - #<?= htmlspecialchars($order_id) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100 min-h-screen">

<?php include("../components/sidebar.php"); ?>
<?php include("../components/Navbar.php"); ?>

<div class="min-h-screen p-6 max-w-4xl mx-auto bg-white rounded-lg shadow-lg mt-10">

    <h1 class="text-3xl font-bold mb-6 text-center">Order Details - #<?= htmlspecialchars($order_id) ?></h1>

    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded <?= stripos($message, 'success') !== false ? 'bg-green-600 text-white' : 'bg-red-600 text-white' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-3 gap-10">

        <!-- Order Info -->
        <div class="md:col-span-1 border-r border-gray-300 pr-6">
            <p><strong>Gig Title:</strong></p>
            <p class="text-lg font-medium mb-4"><?= htmlspecialchars($order['gig_title']) ?></p>

            <p><strong>Status:</strong></p>
            <span class="inline-block px-3 py-1 rounded-full font-semibold text-sm
                <?= $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                <?= $order['status'] === 'active' ? 'bg-blue-100 text-blue-700' : '' ?>
                <?= $order['status'] === 'delivered' ? 'bg-purple-100 text-purple-700' : '' ?>
                <?= $order['status'] === 'cancelled' ? 'bg-red-100 text-red-700' : '' ?>">
                <?= ucfirst($order['status']) ?>
            </span>

            <div class="mt-6 border-t border-gray-300 pt-6 space-y-2">
                <p><strong>Price:</strong> ₹<?= number_format($order['price'], 2) ?></p>
                <p><strong>Ordered on:</strong> <?= date("M d, Y", strtotime($order['created_at'])) ?></p>
                <?php if ($order['updated_at']): ?>
                    <p><strong>Last updated:</strong> <?= date("M d, Y", strtotime($order['updated_at'])) ?></p>
                <?php endif; ?>
            </div>

            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-2">Client Information</h2>
                <p><i class="fa-solid fa-user mr-2"></i> <?= htmlspecialchars($order['client_name']) ?></p>
                <p><i class="fa-solid fa-envelope mr-2"></i> <?= htmlspecialchars($order['client_email']) ?></p>
            </div>
        </div>

        <!-- Update Form -->
        <div class="md:col-span-2">
            <h2 class="text-2xl font-semibold mb-6">Update Order Status & Delivery</h2>

            <?php if ($order['status'] !== 'cancelled'): ?>
            <form method="POST" enctype="multipart/form-data" class="space-y-6" action="">
                <div>
                    <label for="status" class="block font-medium mb-1">Order Status</label>
                    <select id="status" name="status" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                        <?php foreach ($allowed_statuses as $status): ?>
                            <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="delivery_message" class="block font-medium mb-1">Delivery Message (optional)</label>
                    <textarea id="delivery_message" name="delivery_message" rows="4" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Write a message for your client"><?= htmlspecialchars($order['delivery_message'] ?? '') ?></textarea>
                </div>

                <div>
                    <label for="delivery_file" class="block font-medium mb-1">Upload Delivery File (optional, max 10MB)</label>
                    <input type="file" id="delivery_file" name="delivery_file" accept=".zip,.rar,.7zip,.pdf,.doc,.docx,image/*" class="w-full border border-gray-300 p-2 rounded" />
                </div>

                <?php if (!empty($order['delivery_file'])): ?>
                    <div>
                        <p class="font-medium mb-1">Current Delivery File:</p>
                        <a href="../uploads/deliveries/<?= htmlspecialchars($order['delivery_file']) ?>" target="_blank" class="text-blue-600 underline truncate block max-w-md"><?= htmlspecialchars($order['delivery_file']) ?></a>
                    </div>
                <?php endif; ?>

                <button type="submit" class="inline-flex items-center gap-2 bg-[#1DBF73] px-6 py-3 rounded text-white font-semibold hover:bg-green-600 transition focus:outline-none focus:ring-4 focus:ring-green-400">
                    <i class="fa-solid fa-save"></i> Update Order
                </button>
            </form>
            <?php else: ?>
            <div class="text-center text-gray-500 italic font-medium py-20">
                This order is cancelled. No further actions can be taken.
            </div>
            <?php endif; ?>

            <div class="mt-10">
                <?php if ($invoice): ?>
                    <a href="download_invoice.php?invoice=<?= urlencode($invoice['invoice_number']) ?>" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded font-semibold">
                        <i class="fa-solid fa-file-pdf mr-2"></i> Download Invoice
                    </a>
                <?php else: ?>
                    <p class="text-gray-500 italic">Invoice not generated yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
