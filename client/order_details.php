<?php
session_start();
require("../db.php");

// User authentication and role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['id'] ?? 0);

if ($order_id <= 0) {
    echo "Invalid Order ID.";
    exit();
}

// Fetch order details ensuring client owns the order
$stmt = $conn->prepare("
    SELECT o.*, g.title AS gig_title, g.price, g.delivery_time, g.freelancer_id,
           f.name AS freelancer_name, f.email AS freelancer_email
    FROM orders o
    JOIN gigs g ON o.gig_id = g.id
    JOIN users f ON g.freelancer_id = f.id
    WHERE o.id = ? AND o.client_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "Order not found or unauthorized.";
    exit();
}

// Check if review already exists for this gig and client
$stmtReviewCheck = $conn->prepare("SELECT rating, review_text FROM reviews WHERE gig_id = ? AND client_id = ?");
$stmtReviewCheck->bind_param("ii", $order['gig_id'], $user_id);
$stmtReviewCheck->execute();
$resultReview = $stmtReviewCheck->get_result();
$existingReview = $resultReview->fetch_assoc();
$stmtReviewCheck->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Order Details - #<?= htmlspecialchars($order_id) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100 min-h-screen">

<?php include("../components/sidebar.php"); ?>
<?php include("../components/Navbar.php"); ?>

<?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
        <?php if ($_GET['error'] === 'invalid_order'): ?>
            Invalid order request.
        <?php elseif ($_GET['error'] === 'unauthorized'): ?>
            You are not authorized to view this order.
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="min-h-screen p-6 max-w-4xl mx-auto bg-white rounded-lg shadow-lg mt-10">
    <h1 class="text-3xl font-bold mb-6 text-center">Order Details - #<?= htmlspecialchars($order_id) ?></h1>

    <div class="grid md:grid-cols-3 gap-10">
        <!-- Order Info -->
        <div class="md:col-span-1 border-r border-gray-300 pr-6">
            <p><strong>Gig Title:</strong></p>
            <p class="text-lg font-medium mb-4"><?= htmlspecialchars($order['gig_title']) ?></p>

            <p><strong>Status:</strong></p>
            <span class="inline-block px-3 py-1 rounded-full font-semibold text-sm
                <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-700' : '' ?>
                <?= $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                <?= $order['status'] === 'paid' ? 'bg-blue-100 text-blue-700' : '' ?>
                <?= $order['status'] === 'active' ? 'bg-indigo-100 text-indigo-700' : '' ?>
                <?= $order['status'] === 'delivered' ? 'bg-purple-100 text-purple-700' : '' ?>
                <?= $order['status'] === 'cancelled' ? 'bg-red-100 text-red-700' : '' ?>">
                <?= ucfirst($order['status']) ?>
            </span>

            <div class="mt-6 border-t border-gray-300 pt-6 space-y-2">
                <p><strong>Price:</strong> ₹<?= number_format($order['price'], 2) ?></p>
                <p><strong>Ordered on:</strong> <?= date("M d, Y", strtotime($order['created_at'])) ?></p>
                <?php if ($order['updated_at']): ?>
                    <p><strong>Last updated:</strong> <?= date("M d, Y", strtotime($order['updated_at'])) ?></p>
                <?php endif; ?>
            </div>

            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-2">Freelancer Information</h2>
                <p><i class="fa-solid fa-user mr-2"></i> <?= htmlspecialchars($order['freelancer_name']) ?></p>
                <p><i class="fa-solid fa-envelope mr-2"></i> <?= htmlspecialchars($order['freelancer_email']) ?></p>
            </div>
        </div>

        <!-- Client Actions and Delivery Details -->
        <div class="md:col-span-2">
    <h2 class="text-2xl font-semibold mb-6">Actions</h2>

<?php if ($order['status'] === 'delivered'): ?>
    <button id="openPaymentModal" type="button" 
        class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700 mb-6">
        <i class="fa-solid fa-credit-card mr-2"></i> Proceed to Payment
    </button>

<?php elseif ($order['status'] === 'paid'): ?>
    <p class="text-green-600 font-semibold mb-6">
        Payment Completed. Waiting for freelancer to mark order as completed.
    </p>

<?php elseif ($order['status'] === 'completed'): ?>
    <p class="text-green-600 font-semibold mb-4">Order Completed Successfully.</p>
   <a href="Invoice.php?id=<?= intval($order['id']) ?>" 
   class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
   <i class="fa-solid fa-file-invoice mr-2"></i> Download Invoice
</a>


<?php elseif ($order['status'] === 'cancelled'): ?>
    <p class="text-red-600 font-semibold mb-6">Order Cancelled.</p>

<?php else: ?>
    <p class="text-gray-600 mb-6">No actions available at this time.</p>
<?php endif; ?>



            <h2 class="text-2xl font-semibold mb-3 mt-6">Delivery Details</h2>

            <?php if (!empty($order['delivery_file']) || !empty($order['delivery_message'])): ?>
                <section class="bg-gray-50 p-4 rounded shadow-sm">
                    <?php if (!empty($order['delivery_file'])): ?>
                        <p class="mb-2 font-medium">Delivered File:</p>
                        <a href="../uploads/deliveries/<?= htmlspecialchars($order['delivery_file']) ?>"
                           target="_blank"
                           class="text-blue-600 underline break-words">
                           <?= htmlspecialchars($order['delivery_file']) ?>
                        </a>
                    <?php else: ?>
                        <p class="mb-2 italic text-gray-500">No delivery file uploaded yet.</p>
                    <?php endif; ?>

                    <?php if (!empty($order['delivery_message'])): ?>
                        <p class="mt-4 font-medium">Delivery Message:</p>
                        <p class="whitespace-pre-wrap"><?= htmlspecialchars($order['delivery_message']) ?></p>
                    <?php else: ?>
                        <p class="italic text-gray-500 mt-4">No delivery message provided.</p>
                    <?php endif; ?>
                </section>
            <?php else: ?>
                <p class="italic text-gray-500">No delivery details available yet.</p>
            <?php endif; ?>


            <h2 class="text-2xl font-semibold mb-3 mt-8">Messages</h2>
            <a href="chat.php?user_id=<?= $order['freelancer_id'] ?>" class="inline-block bg-purple-600 text-white px-6 py-3 rounded hover:bg-purple-700">
                <i class="fa-solid fa-comments mr-2"></i> Chat with Freelancer
            </a>


            <?php if ($order['status'] === 'completed'): ?>
                <h2 class="text-2xl font-semibold mb-3 mt-8">Leave a Review for This Freelancer</h2>
                <?php if ($existingReview): ?>
                    <p class="mb-4 text-green-600">You have already submitted a review for this gig.</p>
                    <p><strong>Rating:</strong> <?= intval($existingReview['rating']) ?>/5</p>
                    <p><strong>Review:</strong> <?= nl2br(htmlspecialchars($existingReview['review_text'])) ?></p>
                <?php else: ?>
                    <form method="POST" action="submit_review.php" class="mb-6">
                        <input type="hidden" name="gig_id" value="<?= $order['gig_id'] ?>" />
                        <input type="hidden" name="client_id" value="<?= $user_id ?>" />
                        <input type="hidden" name="freelancer_id" value="<?= $order['freelancer_id'] ?>" />
                        <input type="hidden" name="order_id" value="<?= $order_id ?>" />

                        <label for="rating" class="block font-medium mb-2">Rating:</label>
                        <div class="flex space-x-1 mb-4">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required/>
                                <label for="star<?= $i ?>" class="text-yellow-400 text-2xl cursor-pointer">&#9733;</label>
                            <?php endfor; ?>
                        </div>

                        <label for="review_text" class="block font-medium mb-2">Review:</label>
                        <textarea id="review_text" name="review_text" rows="4" class="w-full border rounded p-2 mb-4" required></textarea>

                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">Submit Review</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <h3 class="text-xl font-semibold mb-4">Complete Your Payment</h3>
        <p class="mb-4">Amount to pay: <strong>₹<?= number_format($order['price'], 2) ?></strong></p>

        <form id="paymentForm" method="POST" action="dummy_pay.php">
            <input type="hidden" name="order_id" value="<?= $order_id ?>" />

            <label class="block mb-2 font-medium">Select Payment Method</label>
            <select name="payment_method" required class="border border-gray-300 rounded p-2 w-full mb-4">
                <option value="">Choose a method</option>
                <option value="dummy">Dummy Payment (Test)</option>
                <option value="credit_card">Credit Card</option>
                <option value="upi">UPI</option>
            </select>

            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelPayment" class="px-4 py-2 rounded border border-gray-400 text-gray-700 hover:bg-gray-100">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Pay Now</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('paymentModal');
    var openBtn = document.getElementById('openPaymentModal');
    var cancelBtn = document.getElementById('cancelPayment');

    if (openBtn) {
        openBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });
    }

    cancelBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Optionally auto open modal if order status is delivered
    var orderStatus = "<?= $order['status'] ?>";
    if(orderStatus === 'delivered') {
        modal.classList.remove('hidden');
    }
});
</script>

</body>
</html>

<?php
$conn->close();
?>
