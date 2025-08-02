<?php
session_start();
require("../db.php");

// Check client login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$client_id = $_SESSION['user_id'];

$query = "SELECT o.*, g.title AS gig_title, u.name AS freelancer_name, u.profile_image
          FROM orders o
          JOIN gigs g ON o.gig_id = g.id
          JOIN users u ON o.freelancer_id = u.id
          WHERE o.client_id = ?
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Client Orders - Workloop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <!-- Navbar -->
  <?php include("../components/Navbar.php"); ?>

  <div class="flex min-h-screen">

    <!-- Sidebar -->
    <?php include("../components/sidebar.php"); ?>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 sm:p-8">
      <h1 class="text-2xl font-bold mb-6 text-gray-800">📦 My Orders</h1>

      <?php if ($result->num_rows > 0): ?>
        <div class="grid grid-cols-1 gap-6">
          <?php while ($row = $result->fetch_assoc()): ?>
            <div class="bg-white rounded-xl shadow p-6">
              <div class="flex items-center gap-4 mb-4">
                <img src="../assets/image/user/<?= htmlspecialchars($row['profile_image'] ?? 'default.png') ?>" 
                     alt="Freelancer" 
                     class="w-14 h-14 rounded-full object-cover border border-gray-300">
                <div>
                  <h2 class="text-lg font-semibold"><?= htmlspecialchars($row['gig_title']) ?></h2>
                  <p class="text-sm text-gray-600">by <?= htmlspecialchars($row['freelancer_name']) ?></p>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-gray-700 text-sm">
                <p><span class="font-semibold">Status:</span> <span class="capitalize"><?= $row['status'] ?></span></p>
                <p><span class="font-semibold">Amount:</span> ₹<?= number_format($row['amount'], 2) ?></p>
                <p class="col-span-1 sm:col-span-2"><span class="font-semibold">Requirements:</span> <?= nl2br(htmlspecialchars($row['requirements'])) ?></p>
                <p><span class="font-semibold">Ordered On:</span> <?= date("d M Y", strtotime($row['created_at'])) ?></p>
                <?php if ($row['delivery_file']): ?>
                  <p class="col-span-1 sm:col-span-2">
                    <span class="font-semibold">Delivery File:</span>
                    <a href="../uploads/delivery/<?= htmlspecialchars($row['delivery_file']) ?>" 
                       class="text-blue-600 underline" download>Download</a>
                  </p>
                <?php endif; ?>
              </div>

              <!-- Message Button -->
              <div class="mt-4">
                <a href="message_freelancer.php?freelancer_id=<?= $row['freelancer_id'] ?>" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                  💬 Message Freelancer
                </a>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="text-gray-600">You have no orders yet.</div>
      <?php endif; ?>
    </main>

  </div>
</body>
</html>
