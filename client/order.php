<?php
session_start();
require("../db.php");

// Check if user is a logged-in client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: index.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Validate gig ID
if (!isset($_GET['gig_id'])) {
    header("Location: explore.php");
    exit();
}

$gig_id = intval($_GET['gig_id']);

// Fetch gig details
$stmt = $conn->prepare("SELECT gigs.*, users.name AS freelancer_name, users.id AS freelancer_id
                        FROM gigs
                        JOIN users ON gigs.freelancer_id = users.id
                        WHERE gigs.id = ?");
$stmt->bind_param("i", $gig_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Gig not found.";
    exit();
}

$gig = $result->fetch_assoc();
$freelancer_id = $gig['freelancer_id'];
$amount = $gig['price'];
$message = "";

// Handle order submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $requirements = trim($_POST['requirements']);

    if (empty($requirements)) {
        $message = "Please provide your project requirements.";
    } else {
        $stmt = $conn->prepare("INSERT INTO orders (gig_id, freelancer_id, client_id, amount, status, requirements) 
                                VALUES (?, ?, ?, ?, 'pending', ?)");
        $stmt->bind_param("iiids", $gig_id, $freelancer_id, $client_id, $amount, $requirements);
        if ($stmt->execute()) {
            $message = "✅ Order placed successfully!";
        } else {
            $message = "❌ Something went wrong. Try again.";
        }
    }
}
?>
 <?php include("../components/sidebar.php"); ?>
  <?php include("../components/Navbar.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Gig - Workloop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">

<?php include("../components/Navbar.php"); ?>

<div class="max-w-3xl mx-auto bg-white mt-10 p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Order: <?= htmlspecialchars($gig['title']) ?></h1>

    <div class="mb-6">
        <p><strong>Freelancer:</strong> <?= htmlspecialchars($gig['freelancer_name']) ?></p>
        <p><strong>Price:</strong> ₹<?= $gig['price'] ?></p>
        <p><strong>Delivery Time:</strong> <?= $gig['delivery_time'] ?> days</p>
    </div>

    <?php if ($message): ?>
        <div class="mb-4 text-sm font-semibold text-blue-600"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label class="block mb-2 font-medium">Your Project Requirements:</label>
        <textarea name="requirements" rows="5" class="w-full border p-3 rounded mb-4" placeholder="Describe what you want the freelancer to do..."><?= htmlspecialchars($_POST['requirements'] ?? '') ?></textarea>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Place Order</button>
    </form>
</div>

</body>
</html>
