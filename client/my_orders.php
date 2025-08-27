<?php
session_start();
require("../db.php");

// Protect client access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Fetch client orders
$stmt = $conn->prepare("
    SELECT o.*, g.title AS gig_title, g.image AS gig_image, u.name AS freelancer_name, g.freelancer_id
    FROM orders o
    JOIN gigs g ON o.gig_id = g.id
    JOIN users u ON g.freelancer_id = u.id
    WHERE o.client_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Workloop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
 <?php include("../components/sidebar.php"); ?>
<body class="bg-gray-50">
 <?php include("../components/Navbar.php"); ?>
<div class="flex h-screen">
    <!-- Main -->
    <div class="flex flex-col flex-1 md:ml-64">

        <!-- Content -->
        <main class="flex-1 p-6 overflow-y-auto">
            <?php if ($orders->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition border">
                            <img src="../assets/image/uploads/gigs/<?= htmlspecialchars($order['gig_image']) ?>" 
                                 alt="Gig Image" class="w-full h-40 object-cover rounded-t-xl">
                            <div class="p-4">
                                <h2 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($order['gig_title']) ?></h2>
                                <p class="text-sm text-gray-500">By <?= htmlspecialchars($order['freelancer_name']) ?></p>

                                <!-- Status -->
                                <div class="mt-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-700' : '' ?>
                                        <?= $order['status'] === 'active' ? 'bg-blue-100 text-blue-700' : '' ?>
                                        <?= $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                                        <?= $order['status'] === 'cancelled' ? 'bg-red-100 text-red-700' : '' ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>

                                <!-- Dates -->
                                <div class="mt-3 text-sm text-gray-500">
                                    Ordered: <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                </div>

                                <!-- Button -->
                               <!-- Button -->
                                    <div class="mt-4 flex gap-3">
                                        <a href="order_details.php?id=<?= $order['id'] ?>" 
                                        class="inline-block bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                            View Details
                                        </a>

                                        <!-- Messages Button -->
                                        <a href="chat.php?user_id=<?= $order['freelancer_id'] ?>" 
                                        class="inline-block bg-purple-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                                            💬 Messages
                                        </a>
                                    </div>

                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="bg-white shadow rounded-xl p-6 text-center text-gray-600">
                    You don’t have any orders yet.
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>



</body>
</html>
