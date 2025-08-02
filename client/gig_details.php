<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit;
}

// Check if gig ID is provided
if (!isset($_GET['gig_id']) || empty($_GET['gig_id'])) {
    echo "Gig not found.";
    exit;
}

$gig_id = intval($_GET['gig_id']);

// Fetch gig details
$stmt = $conn->prepare("SELECT g.*, u.name AS freelancer_name, u.profile_image 
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
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($gig['title']) ?> - Gig Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="flex">
  <!-- Sidebar -->
  <?php include("../components/sidebar.php"); ?>

  <div class="flex flex-col w-full">
    <!-- Navbar -->
    <?php include("../components/Navbar.php"); ?>

    <!-- Main Content -->
    <main class="flex-1 p-6 max-w-5xl mx-auto">
      <div class="bg-white shadow-lg rounded-lg p-6">
        <!-- Gig Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($gig['title']) ?></h1>
            <p class="text-sm text-gray-500 mt-1">Category: 
              <span class="font-medium text-indigo-600"><?= htmlspecialchars($gig['category']) ?></span>
            </p>
          </div>
          <div class="mt-4 md:mt-0">
            <span class="inline-block bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">₹<?= htmlspecialchars($gig['price']) ?></span>
            <span class="inline-block ml-3 text-gray-500 text-sm">Delivery: <?= htmlspecialchars($gig['delivery_time']) ?> Days</span>
          </div>
        </div>

        <!-- Gig Image -->
        <div class="mt-6">
          <img src="../assets/image/gigs/<?= htmlspecialchars($gig['image']) ?>" 
               alt="Gig Image" 
               class="w-full rounded-lg object-cover max-h-[300px]">
        </div>

        <!-- Description -->
        <div class="mt-6">
          <h2 class="text-lg font-semibold text-gray-700 mb-2">Description</h2>
          <p class="text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($gig['description'])) ?></p>
        </div>

        <!-- Freelancer Info -->
        <div class="mt-8 border-t pt-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Freelancer</h3>
          <div class="flex items-center space-x-4">
            <img src="../assets/image/user/<?= htmlspecialchars($gig['profile_image']) ?>" 
                 alt="Freelancer Profile" 
                 class="w-12 h-12 rounded-full object-cover">
            <div>
              <p class="text-gray-800 font-medium"><?= htmlspecialchars($gig['freelancer_name']) ?></p>
              <p class="text-gray-500 text-sm">Verified Freelancer</p>
            </div>
          </div>
        </div>

        <!-- Action Button -->
        <div class="mt-8">
          <a href="start_order.php?gig_id=<?= $gig_id ?>" 
             class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
            Order Now
          </a>
        </div>
      </div>
    </main>
  </div>
</div>

</body>
</html>
