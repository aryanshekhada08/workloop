<?php
session_start();
require("../db.php");

// --- Auth check ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id  = $_SESSION['user_id'];
$order_id = intval($_GET['id'] ?? 0);

if ($order_id <= 0) {
    echo "Invalid Order ID.";
    exit();
}

// --- Fetch order details + gig_id (client owns order) ---
$stmt = $conn->prepare("
    SELECT o.*, g.title AS gig_title, g.price, g.delivery_time, g.freelancer_id, g.id AS gig_id,
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

$gig_id = $order['gig_id'];

// --- Check if review exists for this order/gig ---
$stmt = $conn->prepare("SELECT * FROM reviews WHERE client_id = ? AND gig_id = ? LIMIT 1");
$stmt->bind_param("ii", $user_id, $gig_id);
$stmt->execute();
$existingReview = $stmt->get_result()->fetch_assoc();
$stmt->close();
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

<div class="min-h-screen p-6 max-w-4xl mx-auto bg-white rounded-lg shadow-lg mt-10">
    <h1 class="text-3xl font-bold mb-6 text-center">Order Details - #<?= htmlspecialchars($order_id) ?></h1>

    <?php if (isset($_GET['review_submitted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6">
            ✅ Your review has been submitted successfully.
        </div>
    <?php endif; ?>

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

        <!-- Right Side (Actions, Delivery, Messages, Reviews) -->
        <div class="md:col-span-2">
            <h2 class="text-2xl font-semibold mb-6">Actions</h2>

            <?php if ($order['status'] === 'delivered'): ?>
                <button id="openPaymentModal" type="button" class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700 mb-6">
                    <i class="fa-solid fa-credit-card mr-2"></i> Proceed to Payment
                </button>
            <?php elseif ($order['status'] === 'paid'): ?>
                <p class="text-green-600 font-semibold mb-6">Payment Completed. Waiting for freelancer to mark order as completed.</p>
            <?php elseif ($order['status'] === 'completed'): ?>
                <p class="text-green-600 font-semibold mb-4">Order Completed Successfully.</p>
                <a href="Invoice.php?id=<?= intval($order['id']) ?>" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
                    <i class="fa-solid fa-file-invoice mr-2"></i> Send & Download Invoice
                </a>
            <?php elseif ($order['status'] === 'cancelled'): ?>
                <p class="text-red-600 font-semibold mb-6">Order Cancelled.</p>
            <?php else: ?>
                <p class="text-gray-600 mb-6">No actions available at this time.</p>
            <?php endif; ?>

            <!-- Delivery -->
            <h2 class="text-2xl font-semibold mb-3 mt-6">Delivery Details</h2>
            <?php if (!empty($order['delivery_file']) || !empty($order['delivery_message'])): ?>
                <section class="bg-gray-50 p-4 rounded shadow-sm">
                    <?php if (!empty($order['delivery_file'])): ?>
                        <p class="mb-2 font-medium">Delivered File:</p>
                        <a href="../uploads/deliveries/<?= htmlspecialchars($order['delivery_file']) ?>" target="_blank" class="text-blue-600 underline break-words">
                            <?= htmlspecialchars($order['delivery_file']) ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($order['delivery_message'])): ?>
                        <p class="mt-4 font-medium">Delivery Message:</p>
                        <p class="whitespace-pre-wrap"><?= htmlspecialchars($order['delivery_message']) ?></p>
                    <?php endif; ?>
                </section>
            <?php else: ?>
                <p class="italic text-gray-500">No delivery details available yet.</p>
            <?php endif; ?>

            <!-- Messages -->
            <h2 class="text-2xl font-semibold mb-3 mt-8">Messages</h2>
            <a href="chat.php?user_id=<?= $order['freelancer_id'] ?>" class="inline-block bg-purple-600 text-white px-6 py-3 rounded hover:bg-purple-700">
                <i class="fa-solid fa-comments mr-2"></i> Chat with Freelancer
            </a>

            <!-- Reviews -->
            <?php if ($order['status'] === 'completed'): ?>
                <h2 class="text-3xl font-extrabold mb-6 mt-10 text-gray-800">Leave a Review</h2>
                <?php if ($existingReview): ?>
                    <!-- Already Reviewed: Show review and dummy edit button -->
                    <div class="bg-green-100 border border-green-400 text-green-800 p-6 rounded-lg shadow-sm max-w-xl mx-auto">
                        <p class="mb-4 font-semibold text-lg">Your Review:</p>
                        <div class="flex items-center mb-2">
                            <span class="mr-2 text-yellow-500 text-xl">Rating:</span>
                            <span class="text-xl font-bold"><?= intval($existingReview['rating']) ?>/5</span>
                        </div>
                        <p class="whitespace-pre-wrap text-gray-700 leading-relaxed"><?= htmlspecialchars($existingReview['review_text']) ?></p>
                        <button type="button" class="mt-4 px-5 py-2 bg-indigo-400 text-white rounded shadow opacity-70 cursor-not-allowed" title="Edit function coming soon">Edit Review (Dummy)</button>
                    </div>
                <?php else: ?>
                    <!-- Review Form (only if not yet reviewed) -->
                    <form method="POST" action="submit_review.php" class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-lg border border-gray-200">
                        <input type="hidden" name="gig_id" value="<?= htmlspecialchars($order['gig_id']) ?>" />
                        <input type="hidden" name="client_id" value="<?= htmlspecialchars($user_id) ?>" />
                        <input type="hidden" name="freelancer_id" value="<?= htmlspecialchars($order['freelancer_id']) ?>" />
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>" />

                        <label class="block font-semibold mb-3 text-gray-700">Your Rating</label>
                        <div id="starRating" class="flex space-x-1 cursor-pointer mb-4">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg data-value="<?= $i ?>" class="star w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.129 3.46a1 1 0 00.95.69h3.63c.969 0 1.371 1.24.588 1.81l-2.94 2.135a1 1 0 00-.364 1.118l1.13 3.459c.3.921-.755 1.688-1.54 1.118L10 13.347l-2.941 2.135c-.785.57-1.838-.197-1.539-1.118l1.13-3.459a1 1 0 00-.364-1.118L3.346 8.887c-.783-.57-.38-1.81.588-1.81h3.63a1 1 0 00.95-.69l1.13-3.46z"/>
                                </svg>
                                <input type="radio" name="rating" value="<?= $i ?>" class="hidden" required />
                            <?php endfor; ?>
                        </div>
                        <label for="review_text" class="block font-semibold mb-3 text-gray-700">Write Your Review</label>
                        <textarea id="review_text" name="review_text" rows="5" class="w-full border rounded-md p-3" required></textarea>

                        <button type="submit" class="mt-6 w-full bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-3 rounded shadow">
                            Submit Review
                        </button>
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
        <form method="POST" action="dummy_pay.php">
            <input type="hidden" name="order_id" value="<?= $order_id ?>" />
            <select name="payment_method" required class="border rounded p-2 w-full mb-4">
                <option value="">Choose a method</option>
                <option value="dummy">Dummy Payment (Test)</option>
                <option value="credit_card">Credit Card</option>
                <option value="upi">UPI</option>
            </select>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelPayment" class="px-4 py-2 rounded border">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Pay Now</button>
            </div>
        </form>
    </div>
</div>

<script>
// Payment modal toggle
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('paymentModal');
    const openBtn = document.getElementById('openPaymentModal');
    const cancelBtn = document.getElementById('cancelPayment');
    if (openBtn) openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
    if (cancelBtn) cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
});

// Star rating UI
document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('#starRating svg.star');
    const radios = document.querySelectorAll('#starRating input[type=radio]');
    let selected = 0;

    function fillStars(n) {
        stars.forEach((s, i) => s.classList.toggle('text-yellow-400', i < n));
    }

    stars.forEach((star, i) => {
        star.addEventListener('mouseover', () => fillStars(i+1));
        star.addEventListener('mouseout', () => fillStars(selected));
        star.addEventListener('click', () => {
            selected = i+1;
            radios[i].checked = true;
            fillStars(selected);
        });
    });
});
</script>
</body>
</html>
<?php $conn->close(); ?>
