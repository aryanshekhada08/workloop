<?php
session_start();
require("../db.php");

// Redirect if not a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

// Validate gig_id param
if (empty($_GET['gig_id'])) {
    die("Gig not specified.");
}

$gig_id = intval($_GET['gig_id']);

// Fetch gig details for display
$stmt = $conn->prepare("SELECT g.*, u.name AS freelancer_name, u.profile_image, g.freelancer_id FROM gigs g JOIN users u ON g.freelancer_id = u.id WHERE g.id = ?");
$stmt->bind_param("i", $gig_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Gig not found.");
}

$gig = $result->fetch_assoc();

$requirements = "";
$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requirements = trim($_POST['requirements'] ?? '');

    if (empty($requirements)) {
        $errors[] = "Please provide your requirements.";
    }

    if (empty($errors)) {
        $client_id = $_SESSION['user_id'];
        $freelancer_id = $gig['freelancer_id'];
        $amount = $gig['price'];
        $status = "pending";
        $created_at = date('Y-m-d H:i:s');

        // Insert order
        $insertStmt = $conn->prepare("INSERT INTO orders (gig_id, freelancer_id, client_id, amount, status, requirements, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("iiidsss", $gig_id, $freelancer_id, $client_id, $amount, $status, $requirements, $created_at);

        if ($insertStmt->execute()) {
            $order_id = $conn->insert_id;

            // Insert initial message to freelancer
            $message_text = $requirements;
            $msgStmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $msgStmt->bind_param("iis", $client_id, $freelancer_id, $message_text);
            $msgStmt->execute();
            $msgStmt->close();

            $_SESSION['success'] = "Order placed successfully! You can view your orders in your dashboard.";
            header("Location: orders.php");
            exit();
        } else {
            $errors[] = "Failed to place order. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Order Gig: <?= htmlspecialchars($gig['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<?php include("../components/Navbar.php"); ?>
<?php include("../components/sidebar.php"); ?>

<div class="max-w-3xl mx-auto my-12 p-6 bg-white rounded-lg shadow">

    <h1 class="text-3xl font-bold mb-6">Order: <?= htmlspecialchars($gig['title']) ?></h1>

    <div class="mb-4">
        <p class="text-gray-700 font-semibold">Price: ₹<?= number_format($gig['price'], 2) ?></p>
        <p class="text-gray-600">Delivery Time: <?= htmlspecialchars($gig['delivery_time']) ?> days</p>
        <p class="text-gray-600 mt-2"><?= nl2br(htmlspecialchars($gig['description'])) ?></p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc px-5">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (!empty($_SESSION['success'])): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="POST" novalidate>
        <label for="requirements" class="block font-medium mb-2">Your Requirements <span class="text-red-600">*</span></label>
        <textarea id="requirements" name="requirements" rows="5" required class="w-full border rounded p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($requirements) ?></textarea>

        <button type="submit" class="mt-6 bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700 transition">
            Place Order
        </button>
    </form>
</div>

</body>
</html>
