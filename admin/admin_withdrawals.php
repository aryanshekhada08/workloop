<?php
session_start();
require("../db.php");

// Check admin authentication
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../index.php");
//     exit();
// }

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdrawal_id'], $_POST['action'])) {
    $withdrawal_id = (int)$_POST['withdrawal_id'];
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        if ($action === 'rejected') {
            // Refund logic
            $conn->begin_transaction();
            try {
                // Get withdrawal details
                $stmt = $conn->prepare("SELECT freelancer_id, amount FROM withdrawals WHERE id = ?");
                $stmt->bind_param("i", $withdrawal_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows !== 1) {
                    throw new Exception("Withdrawal request not found.");
                }
                $withdrawal = $result->fetch_assoc();
                $stmt->close();

                $freelancerId = (int)$withdrawal['freelancer_id'];
                $amount = (float)$withdrawal['amount'];

                // Check wallet existence
                $walletCheck = $conn->prepare("SELECT balance FROM freelancer_wallets WHERE freelancer_id = ?");
                $walletCheck->bind_param("i", $freelancerId);
                $walletCheck->execute();
                $walletCheck->store_result();
                $walletExists = $walletCheck->num_rows > 0;
                $walletCheck->close();

                if ($walletExists) {
                    $updateWallet = $conn->prepare("UPDATE freelancer_wallets SET balance = balance + ? WHERE freelancer_id = ?");
                    $updateWallet->bind_param("di", $amount, $freelancerId);
                    if (!$updateWallet->execute()) {
                        throw new Exception("Failed to update freelancer wallet.");
                    }
                    $updateWallet->close();
                } else {
                    $insertWallet = $conn->prepare("INSERT INTO freelancer_wallets (freelancer_id, balance) VALUES (?, ?)");
                    $insertWallet->bind_param("id", $freelancerId, $amount);
                    if (!$insertWallet->execute()) {
                        throw new Exception("Failed to create freelancer wallet.");
                    }
                    $insertWallet->close();
                }

                // Update withdrawal status
                $updateStatus = $conn->prepare("UPDATE withdrawals SET status = ? WHERE id = ?");
                $updateStatus->bind_param("si", $action, $withdrawal_id);
                if (!$updateStatus->execute()) {
                    throw new Exception("Failed to update withdrawal status.");
                }
                $updateStatus->close();

                $conn->commit();
                $_SESSION['success'] = "Withdrawal request rejected and amount refunded to freelancer's wallet.";
            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['error'] = "Error during refund: " . $e->getMessage();
            }
        } else {
            // Approve action - just update status
            $stmt = $conn->prepare("UPDATE withdrawals SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $action, $withdrawal_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Withdrawal request approved.";
            } else {
                $_SESSION['error'] = "Failed to update the withdrawal request.";
            }
            $stmt->close();
        }

        header("Location: admin_withdrawals.php");
        exit();
    }
}

// Fetch withdrawal requests with freelancer info
$query = "
    SELECT w.*, u.name AS freelancer_name, u.email AS freelancer_email 
    FROM withdrawals w
    LEFT JOIN users u ON w.freelancer_id = u.id
    ORDER BY w.requested_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Admin - Freelancer Withdrawals</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <?php include("../admin/includes/sidebar.php"); ?>

    <!-- Main content -->
    <div class="flex-1 p-8 ml-34 overflow-auto max-w-8xl">

        <!-- Session messages -->
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-600 text-white rounded shadow">
                <?= htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-600 text-white rounded shadow">
                <?= htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <h1 class="text-3xl font-semibold mb-6">Freelancer Withdrawal Requests</h1>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Freelancer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount (₹)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['id']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['freelancer_name'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['freelancer_email'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= number_format($row['amount'], 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['method'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= date('M d, Y H:i', strtotime($row['requested_at'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize font-semibold">
                                <?php if ($row['status'] === 'pending'): ?>
                                    <span class="text-yellow-600 uppercase"><?= htmlspecialchars($row['status']) ?></span>
                                <?php elseif ($row['status'] === 'approved'): ?>
                                    <span class="text-green-600 uppercase"><?= htmlspecialchars($row['status']) ?></span>
                                <?php elseif ($row['status'] === 'rejected'): ?>
                                    <span class="text-red-600 uppercase"><?= htmlspecialchars($row['status']) ?></span>
                                <?php else: ?>
                                    <?= htmlspecialchars($row['status']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                <?php if ($row['status'] === 'pending'): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="withdrawal_id" value="<?= (int)$row['id'] ?>">
                                        <button type="submit" name="action" value="approved"
                                            class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">Approve</button>
                                        <button type="submit" name="action" value="rejected"
                                            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">Reject</button>
                                    </form>
                                <?php else: ?>
                                    <span class="italic text-gray-500">No actions available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
