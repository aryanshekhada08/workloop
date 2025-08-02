<?php
session_start();
require("../db.php");

// Protect freelancer access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$freelancer_id = $_SESSION['user_id'];

// Fetch orders for this freelancer
$stmt = $conn->prepare("SELECT o.*, g.title AS gig_title, u.name AS client_name 
                        FROM orders o 
                        JOIN gigs g ON o.gig_id = g.id 
                        JOIN users u ON o.client_id = u.id 
                        WHERE o.freelancer_id = ? 
                        ORDER BY o.created_at DESC");
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders - Freelancer | Workloop</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <?php include("../components/Navbar.php"); ?>

  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">My Orders</h1>

    <?php if ($result->num_rows > 0): ?>
      <div class="grid gap-4">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="bg-white shadow rounded-lg p-5 border">
            <div class="flex justify-between items-center mb-2">
              <h2 class="text-lg font-semibold"><?= htmlspecialchars($row['gig_title']) ?></h2>
              <span class="text-sm text-gray-500"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
            </div>
            <p><strong>Client:</strong> <?= htmlspecialchars($row['client_name']) ?></p>
            <p><strong>Amount:</strong> ₹<?= number_format($row['amount'], 2) ?></p>
            <p><strong>Status:</strong> <span class="text-blue-600 font-semibold"><?= ucfirst($row['status']) ?></span></p>

            <div class="mt-4 flex gap-3">
              <?php if ($row['status'] === 'active'): ?>
                <a href="deliver_order.php?order_id=<?= $row['id'] ?>" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Deliver Work</a>
              <?php endif; ?>
              <a href="chat.php?user_id=<?= $row['client_id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Message Client</a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="text-gray-600">You haven't received any orders yet.</p>
    <?php endif; ?>
  </div>
</body>
</html>
