<?php
session_start();
require("../db.php");

// Check admin login and role

// Fetch total counts (example queries)
$totalUsers = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetch_row()[0];
$totalFreelancers = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role = 'freelancer'")->fetch_row()[0];
$totalOrders = (int)$conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$totalPendingPayments = (int)$conn->query("SELECT COUNT(*) FROM orders WHERE status = 'paid' AND platform_fee > 0")->fetch_row()[0];

// Fetch recent orders (simple example)
$ordersResult = $conn->query("
    SELECT o.id, o.status, o.amount, o.created_at, 
           c.name as client_name, f.name as freelancer_name 
    FROM orders o
    LEFT JOIN users c ON o.client_id = c.id
    LEFT JOIN users f ON o.freelancer_id = f.id
    ORDER BY o.created_at DESC LIMIT 10
");

// Fetch recent chat summary (example)
$chatsResult = $conn->query("
    SELECT sender_id, receiver_id, MAX(sent_at) as last_message_date, COUNT(*) as message_count 
    FROM messages 
    GROUP BY sender_id, receiver_id
    ORDER BY last_message_date DESC LIMIT 10
");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard - Workloop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg flex flex-col fixed h-screen">
        <?php include("../admin/includes/sidebar.php"); ?>
        <div class="p-6 border-t border-gray-300">
            <a href="logout.php" class="block px-3 py-2 rounded bg-red-600 text-white text-center hover:bg-red-700 font-semibold">Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-8 overflow-auto max-w-8xl">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></p>
        </header>

        <!-- Overview cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <h2 class="text-3xl font-bold text-green-600"><?= $totalUsers ?></h2>
                <p class="text-gray-600 mt-1">Clients</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <h2 class="text-3xl font-bold text-blue-600"><?= $totalFreelancers ?></h2>
                <p class="text-gray-600 mt-1">Freelancers</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <h2 class="text-3xl font-bold text-purple-600"><?= $totalOrders ?></h2>
                <p class="text-gray-600 mt-1">Orders</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <h2 class="text-3xl font-bold text-red-600"><?= $totalPendingPayments ?></h2>
                <p class="text-gray-600 mt-1">Pending Payments</p>
            </div>
        </div>

        <!-- Orders Table -->
        <section class="mb-10">
            <h2 class="text-2xl font-semibold mb-4">Recent Orders</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-md overflow-hidden shadow border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Order ID</th>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Client</th>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Freelancer</th>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Amount</th>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Status</th>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $ordersResult->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 border-b border-gray-200">
                            <td class="py-3 px-6"><?= htmlspecialchars($order['id']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($order['client_name']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($order['freelancer_name']) ?></td>
                            <td class="py-3 px-6">₹<?= number_format($order['amount'], 2) ?></td>
                            <td class="py-3 px-6 capitalize"><?= htmlspecialchars($order['status']) ?></td>
                            <td class="py-3 px-6"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Chat Summary -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Recent Chat Conversations</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-md overflow-hidden shadow border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Sender ID</th>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Receiver ID</th>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Last Message Date</th>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Messages Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($chat = $chatsResult->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 border-b border-gray-200">
                            <td class="py-3 px-6"><?= htmlspecialchars($chat['sender_id']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($chat['receiver_id']) ?></td>
                            <td class="py-3 px-6"><?= date('M d, Y H:i', strtotime($chat['last_message_date'])) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($chat['message_count']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

</body>
</html>
