<?php
session_start();
require("../db.php");

// Access control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$freelancer_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'] ?? null;
$message = "";

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND freelancer_id = ?");
$stmt->bind_param("ii", $order_id, $freelancer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found or unauthorized access.");
}

// Handle submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $delivery_message = trim($_POST['delivery_message']);
    $file_name = null;

    if (isset($_FILES['delivery_file']) && $_FILES['delivery_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['delivery_file']['tmp_name'];
        $file_name = time() . "_" . basename($_FILES['delivery_file']['name']);
        $target_path = "../uploads/deliveries/" . $file_name;

        if (!is_dir("../uploads/deliveries")) {
            mkdir("../uploads/deliveries", 0777, true);
        }

        move_uploaded_file($file_tmp, $target_path);
    }

    // Update order
    $update = $conn->prepare("UPDATE orders SET status='delivered', delivery_file=?, delivery_message=? WHERE id=?");
    $update->bind_param("ssi", $file_name, $delivery_message, $order_id);
    $update->execute();

    // Notify client
    $notify = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $msg = "Your order for gig #" . $order['gig_id'] . " has been delivered.";
    $notify->bind_param("is", $order['client_id'], $msg);
    $notify->execute();

    $message = "Delivery submitted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Deliver Order | Workloop</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <?php include("../components/Navbar.php"); ?>

  <div class="max-w-xl mx-auto mt-10 bg-white p-6 shadow rounded-lg">
    <h2 class="text-xl font-semibold mb-4">Deliver Order</h2>

    <?php if ($message): ?>
      <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Attach Delivery File (ZIP, PDF, etc.)</label>
        <input type="file" name="delivery_file" required class="w-full border p-2 rounded">
      </div>

      <div>
        <label class="block mb-1 font-medium">Message (optional)</label>
        <textarea name="delivery_message" rows="4" class="w-full border p-2 rounded"></textarea>
      </div>

      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Submit Delivery
      </button>
    </form>
  </div>
</body>
</html>
