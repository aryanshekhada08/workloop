<?php
session_start();
require("../db.php");

// Check admin login
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../index.php");
//     exit();
// }

// Fetch all completed orders with platform fees and related info
$query = "
    SELECT o.id, o.platform_fee, o.amount, o.created_at,
           c.name AS client_name, f.name AS freelancer_name
    FROM orders o
    LEFT JOIN users c ON o.client_id = c.id
    LEFT JOIN users f ON o.freelancer_id = f.id
    WHERE o.status = 'completed' AND o.platform_fee > 0
    ORDER BY o.created_at DESC
";
$result = $conn->query($query);

// Calculate total platform fees collected
$totalFeesResult = $conn->query("SELECT SUM(platform_fee) FROM orders WHERE status = 'completed' AND platform_fee > 0");
$totalFees = $totalFeesResult->fetch_row()[0] ?? 0;
$totalFees = number_format((float)$totalFees, 2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Admin - Platform Fees</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <?php include("../admin/includes/sidebar.php"); ?>

    <!-- Main content -->
    <div class="flex-1 p-8 ml-34 overflow-auto max-w-8xl">

        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Platform Fee Overview</h1>
            <p class="text-gray-600 mt-1">Total platform fees collected: <strong>₹<?= $totalFees ?></strong></p>
        </header>

        <section class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Freelancer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Amount (₹)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Platform Fee (₹)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">No completed orders with fees found.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['id']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['client_name'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['freelancer_name'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">₹<?= number_format($row['amount'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-600 font-semibold">₹<?= number_format($row['platform_fee'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </div>
</body>

</html>
