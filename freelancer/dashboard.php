<?php
session_start();
require("../db.php");

// Ensure freelancer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'Freelancer';

// Fetch stats
$gigCount = $conn->query("SELECT COUNT(*) FROM gigs WHERE freelancer_id = $user_id")->fetch_row()[0];
$orderCount = $conn->query("SELECT COUNT(*) FROM orders WHERE freelancer_id = $user_id")->fetch_row()[0];
$completedCount = $conn->query("SELECT COUNT(*) FROM orders WHERE freelancer_id = $user_id AND status = 'completed'")->fetch_row()[0];

// Total earnings (sum of amounts of completed orders)
$earningResult = $conn->query("SELECT SUM(amount) FROM orders WHERE freelancer_id = $user_id AND status = 'completed'");
$totalEarnings = $earningResult->fetch_row()[0] ?? 0.00;

// Total platform fees on completed orders
$feeResult = $conn->query("SELECT SUM(platform_fee) FROM orders WHERE freelancer_id = $user_id AND status = 'completed'");
$totalFees = $feeResult->fetch_row()[0] ?? 0.00;

// Wallet balance
$walletStmt = $conn->prepare("SELECT balance FROM freelancer_wallets WHERE freelancer_id = ?");
$walletStmt->bind_param("i", $user_id);
$walletStmt->execute();
$walletResult = $walletStmt->get_result();
$walletRow = $walletResult->fetch_assoc();
$walletBalance = $walletRow ? $walletRow['balance'] : 0.00;

// Recent orders (limit 5)
$recentOrdersResult = $conn->query("
    SELECT o.id, o.amount, o.status, o.created_at, o.deadline, u.name AS client_name
    FROM orders o
    JOIN users u ON o.client_id = u.id
    WHERE o.freelancer_id = $user_id
    ORDER BY o.created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Freelancer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100 font-sans">

<?php include("../components/sidebar.php"); ?>
<?php include("../components/Navbar.php"); ?>

<div class="ml-64 p-6">

    <h1 class="text-3xl font-bold mb-6">Welcome, <?= htmlspecialchars($user_name) ?> 👋</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
        <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition flex items-center space-x-4">
            <i class="fa-solid fa-briefcase text-green-600 text-3xl"></i>
            <div>
                <p class="text-gray-500">Total Gigs</p>
                <p class="text-2xl font-bold"><?= $gigCount ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition flex items-center space-x-4">
            <i class="fa-solid fa-box text-green-600 text-3xl"></i>
            <div>
                <p class="text-gray-500">Total Orders</p>
                <p class="text-2xl font-bold"><?= $orderCount ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition flex items-center space-x-4">
            <i class="fa-solid fa-check-circle text-green-600 text-3xl"></i>
            <div>
                <p class="text-gray-500">Completed Orders</p>
                <p class="text-2xl font-bold"><?= $completedCount ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition flex flex-col items-start space-y-2">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-indian-rupee-sign text-green-600 text-3xl"></i>
                <p class="text-gray-500">Total Earnings</p>
            </div>
            <p class="text-3xl font-bold text-green-600"><?= number_format($totalEarnings, 2) ?></p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition flex flex-col items-start space-y-2">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-wallet text-green-600 text-3xl"></i>
                <p class="text-gray-500">Wallet Balance</p>
            </div>
            <p class="text-3xl font-bold text-green-600">₹<?= number_format($walletBalance, 2) ?></p>
            <p class="text-sm text-gray-400">Platform Fees Paid: ₹<?= number_format($totalFees, 2) ?></p>
        </div>
    </div>

    <section class="bg-white rounded-xl shadow p-6">
        <h2 class="text-2xl mb-4 font-semibold">Recent Orders</h2>

        <?php if ($recentOrdersResult->num_rows > 0): ?>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="text-left p-2">Order ID</th>
                        <th class="text-left p-2">Client Name</th>
                        <th class="text-left p-2">Amount</th>
                        <th class="text-left p-2">Status</th>
                        <th class="text-left p-2">Created</th>
                        <th class="text-left p-2">Deadline</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $recentOrdersResult->fetch_assoc()): ?>
                        <?php
                        $deadline = new DateTime($row['deadline']);
                        $today = new DateTime('today');
                        $tomorrow = new DateTime('tomorrow');
                        if (($deadline == $today || $deadline == $tomorrow) && $deadline >= $today) {
                            $deadlineClass = "text-yellow-600 font-semibold";
                        } else {
                            $deadlineClass = "";
                        }
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2"><?= (int)$row['id'] ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['client_name']) ?></td>
                            <td class="p-2">₹<?= number_format($row['amount'], 2) ?></td>
                            <td class="p-2">
                                <span class="inline-block px-3 py-1 rounded-full text-white text-sm
                                    <?php
                                        if ($row['status'] === 'completed') echo 'bg-green-600';
                                        elseif ($row['status'] === 'pending') echo 'bg-yellow-500';
                                        elseif ($row['status'] === 'cancelled' || $row['status'] === 'rejected') echo 'bg-red-600';
                                        else echo 'bg-gray-500';
                                    ?>">
                                    <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                </span>
                            </td>
                            <td class="p-2"><?= date("d M Y", strtotime($row['created_at'])) ?></td>
                            <td class="p-2 <?= $deadlineClass ?>">
                                <?= date("d M Y", strtotime($row['deadline'])) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500">No recent orders found.</p>
        <?php endif; ?>
    </section>

</div>

</body>
</html>
<?php include("../components/footer.php"); ?>

