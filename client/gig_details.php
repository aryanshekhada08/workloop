<?php
session_start();
require("../db.php");

// Redirect if not client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit;
}

// Validate gig_id param
if (empty($_GET['gig_id'])) {
    echo "Gig not found.";
    exit;
}

$gig_id = intval($_GET['gig_id']);

// Fetch gig details with freelancer info
$stmt = $conn->prepare("
    SELECT g.*, u.name AS freelancer_name, u.profile_image 
    FROM gigs g 
    JOIN users u ON g.freelancer_id = u.id 
    WHERE g.id = ?");
$stmt->bind_param("i", $gig_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Gig not found.";
    exit;
}

$gig = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($gig['title']) ?> - Gig Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<?php include("../components/Navbar.php"); ?>
<?php include("../components/sidebar.php"); ?>


<div class="max-w-4xl mx-auto py-12 px-6">

  <div class="bg-white p-8 rounded-lg shadow-md space-y-6">

    <h1 class="text-3xl font-extrabold text-gray-900"><?= htmlspecialchars($gig['title']) ?></h1>

    <div class="flex items-center space-x-6">
      <img src="../assets/image/uploads/gigs/<?= htmlspecialchars($gig['image']) ?>" alt="Gig Image" class="w-64 h-40 object-cover rounded-lg">
      <div>
        <p class="text-indigo-600 font-semibold text-xl">₹<?= htmlspecialchars($gig['price']) ?></p>
        <p class="text-gray-600 mt-2">Category: <?= htmlspecialchars($gig['category']) ?></p>
        <p class="text-gray-600 mt-1">Delivery Time: <?= htmlspecialchars($gig['delivery_time']) ?> days</p>
      </div>
    </div>

    <section>
      <h2 class="text-xl font-semibold mb-2">Description</h2>
      <p class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($gig['description']) ?></p>
    </section>

    <section class="flex items-center space-x-4">
      <img src="../assets/image/user/<?= htmlspecialchars($gig['profile_image'] ?? 'default.png') ?>" alt="Freelancer" class="w-10 h-10 rounded-full object-cover border" />
      <div>
        <p class="font-medium text-gray-900"><?= htmlspecialchars($gig['freelancer_name']) ?></p>
        <p class="text-sm text-gray-500">Verified Freelancer</p>
      </div>
    </section>

    <a href="start_order.php?gig_id=<?= $gig_id ?>" 
       class="block text-center bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
      Order Now
    </a>

  </div>

</div>
<!-- Your existing HTML and PHP code -->

<?php
if (isset($_SESSION['success'])): ?>
    <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded shadow bg-green-600 text-white font-semibold z-50">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
<?php
    unset($_SESSION['success']);
endif;

if (isset($_SESSION['error'])): ?>
    <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded shadow bg-red-600 text-white font-semibold z-50">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
<?php
    unset($_SESSION['error']);
endif;
?>

<script>
  setTimeout(() => {
    const successBox = document.querySelector('.bg-green-600');
    const errorBox = document.querySelector('.bg-red-600');
    if (successBox) {
      successBox.style.transition = "opacity 0.5s ease";
      successBox.style.opacity = 0;
      setTimeout(() => successBox.remove(), 500);
    }
    if (errorBox) {
      errorBox.style.transition = "opacity 0.5s ease";
      errorBox.style.opacity = 0;
      setTimeout(() => errorBox.remove(), 500);
    }
  }, 3000);
</script>

</body>
</html>

</body>

</html>
