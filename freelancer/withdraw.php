<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$freelancer_id = $_SESSION['user_id'];

// Fetch current balance from freelancer_wallets table
$stmt = $conn->prepare("SELECT balance FROM freelancer_wallets WHERE freelancer_id = ?");
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$stmt->bind_result($balance);
if (!$stmt->fetch()) {
    // No record yet, consider zero balance
    $balance = 0;
}
$stmt->close();

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_withdraw'])) {
    $withdraw_amount = floatval($_POST['amount']);
    $payment_method = trim($_POST['payment_method']);
    $payment_details = trim($_POST['payment_details']);

    if ($withdraw_amount <= 0) {
        $error = "Withdrawal amount must be greater than zero.";
    } elseif ($withdraw_amount > $balance) {
        $error = "Withdrawal amount cannot exceed your available balance.";
    } elseif (empty($payment_method) || empty($payment_details)) {
        $error = "Please select a payment method and enter payment details.";
    } else {
        // Use transaction to safely insert withdrawal and deduct balance
        $conn->begin_transaction();

        try {
            // Insert into withdrawals table
            $insert_stmt = $conn->prepare("INSERT INTO withdrawals (freelancer_id, amount, method, status, requested_at) VALUES (?, ?, ?, 'pending', NOW())");
            $insert_stmt->bind_param("ids", $freelancer_id, $withdraw_amount, $payment_method);
            $insert_stmt->execute();
            $insert_stmt->close();

            // Deduct balance in freelancer_wallets
            $update_stmt = $conn->prepare("UPDATE freelancer_wallets SET balance = balance - ? WHERE freelancer_id = ?");
            $update_stmt->bind_param("di", $withdraw_amount, $freelancer_id);
            $update_stmt->execute();
            $update_stmt->close();

            $conn->commit();

            $message = "Withdrawal request submitted successfully.";
            $balance -= $withdraw_amount; // Reflect new balance in UI
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to submit withdrawal request. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <title>Withdraw Funds - Freelancer | WorkLoop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <?php include '../components/Navbar.php'; ?>
    <div class="flex flex-1 overflow-hidden">
        <?php include '../components/sidebar.php'; ?>

        <main class="flex-grow max-w-lg mx-auto p-6 bg-white rounded-lg shadow-md my-6">
            <h1 class="text-3xl font-semibold mb-6 text-green-700 select-none">Withdraw Funds</h1>

            <p class="mb-4">
                Available Balance: <span class="font-bold text-green-700">₹<?= number_format($balance, 2) ?></span>
            </p>

            <?php if ($message): ?>
                <div class="mb-4 py-2 px-4 bg-green-100 border border-green-400 text-green-700 rounded"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-4 py-2 px-4 bg-red-100 border border-red-400 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" class="space-y-6">
                <div>
                    <label class="block mb-1 font-medium" for="amount">Amount to Withdraw</label>
                    <input type="number" step="0.01" min="0.01" max="<?= htmlspecialchars($balance) ?>" name="amount" id="amount" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />
                    <p class="text-sm text-gray-600 mt-1">Enter an amount up to your available balance.</p>
                </div>

                <div>
                    <label class="block mb-1 font-medium" for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700">
                        <option value="">-- Select payment method --</option>
                        <option value="paypal">PayPal</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="crypto">Cryptocurrency</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium" for="payment_details">Payment Details</label>
                    <textarea name="payment_details" id="payment_details" rows="3" required
                        placeholder="Enter your PayPal email, bank account details, or crypto wallet address"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700"></textarea>
                </div>

                <button type="submit" name="request_withdraw"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition">
                    Submit Withdrawal Request
                </button>
            </form>

        </main>
    </div>

</body>

</html>
