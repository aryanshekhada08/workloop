<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

require("../db.php");

$userId = $_SESSION['user_id'];


// Handle Cancel Order action
if (isset($_GET['cancel_order'])) {
    $cancelId = (int)$_GET['cancel_order'];
    $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND client_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $cancelId, $userId);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit();
}

// Fetch client orders with gig title
$stmt = $conn->prepare("
  SELECT orders.id, gigs.title, orders.status, orders.created_at 
  FROM orders 
  JOIN gigs ON orders.gig_id = gigs.id 
  WHERE orders.client_id = ? 
  ORDER BY orders.created_at DESC
  LIMIT 20
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$ordersResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Client Dashboard - Workloop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100">

<!-- Mobile Navbar -->
<div class="md:hidden bg-white shadow p-4 flex justify-between items-center">
    <span class="font-bold text-lg">Workloop</span>
    <button id="menu-btn" class="focus:outline-none" aria-label="Toggle menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>

<div class="flex">
    <?php include("../components/sidebar.php"); ?>

    <div class="flex-1 flex flex-col min-h-screen md:ml-64">

        <?php include("../components/navbar.php"); ?>

        <main class="flex-1 p-6 overflow-auto">

            <h1 class="text-2xl font-bold mb-6">Welcome, <?= htmlspecialchars($_SESSION['name'] ?? '') ?> 👋</h1>

            <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mb-8">
                <!-- Dashboard cards -->
                <div class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                    <h2 class="text-lg font-semibold mb-2">My Orders</h2>
                    <p class="text-gray-600 mb-4">Track and manage your active & past orders.</p>
                    <a href="my_orders.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">View Orders</a>
                </div>
                <div class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                    <h2 class="text-lg font-semibold mb-2">Explore Services</h2>
                    <p class="text-gray-600 mb-4">Find talented freelancers for your projects.</p>
                    <a href="explore.php" class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Browse Now</a>
                </div>
                <div class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                    <h2 class="text-lg font-semibold mb-2">Messages</h2>
                    <p class="text-gray-600 mb-4">Stay connected with freelancers.</p>
                    <a href="chat.php" class="inline-block bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">Open Chat</a>
                </div>
            </div>

            <!-- Manage Orders table -->
            <section class="bg-white rounded-xl shadow p-5">
                <h2 class="text-xl font-semibold mb-4">Manage Orders</h2>

                <?php if ($ordersResult->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border border-gray-300 rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border-b border-gray-300">Order ID</th>
                                <th class="px-4 py-2 border-b border-gray-300">Gig Title</th>
                                <th class="px-4 py-2 border-b border-gray-300">Status</th>
                                <th class="px-4 py-2 border-b border-gray-300">Date</th>
                                <th class="px-4 py-2 border-b border-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $ordersResult->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 even:bg-white odd:bg-gray-50">
                                <td class="px-4 py-3 border-b border-gray-200"><?= htmlspecialchars($row['id']) ?></td>
                                <td class="px-4 py-3 border-b border-gray-200"><?= htmlspecialchars($row['title']) ?></td>
                                <td class="px-4 py-3 border-b border-gray-200"><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                                <td class="px-4 py-3 border-b border-gray-200"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                <td class="px-4 py-3 border-b border-gray-200 space-x-2">
                                    <a href="order_details.php?id=<?= (int)$row['id'] ?>" class="text-blue-600 hover:underline">View</a>

                                    <?php if ($row['status'] === 'pending'): ?>
                                        <a href="?cancel_order=<?= (int)$row['id'] ?>"
                                        class="text-red-600 hover:underline"
                                        onclick="return confirm('Are you sure you want to cancel this pending order?');">Cancel</a>
                                    <?php endif; ?>

                                    <?php if (!in_array($row['status'], ['completed', 'cancelled'])): ?>
                                        <a href="confirm_order.php?id=<?= (int)$row['id'] ?>"
                                        class="text-green-600 hover:underline"
                                         onclick="return confirm('Mark this order as completed?');">Complete</a>
                                    <?php elseif (in_array($row['status'], ['completed'])): ?>
                                        <span class="text-gray-400 italic">Completed</span>
                                    <?php elseif (in_array($row['status'], ['cancelled'])): ?>
                                        <span class="text-gray-400 italic">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-gray-600">You have no orders yet.</p>
                <?php endif; ?>
            </section>

        </main>
    </div>
</div>

<script>
  const menuBtn = document.getElementById('menu-btn');
  const sidebar = document.getElementById('sidebar');
  menuBtn.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
  });
</script>

<?php include("../components/footer.php"); ?>

</body>
</html>
