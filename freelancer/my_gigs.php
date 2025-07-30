<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch gigs
$sql = "SELECT * FROM gigs WHERE freelancer_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Gigs</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100">

  <?php include("../components/sidebar.php"); ?>
  <?php include("../components/Navbar.php"); ?>


  <div class="ml-64 p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">My Gigs</h1>
      <a href="add_gig.php" class="bg-[#1DBF73] text-white px-4 py-2 rounded hover:bg-green-600 transition">
        <i class="fa-solid fa-plus mr-2"></i> Add New Gig
      </a>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($gig = $result->fetch_assoc()): ?>
          <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
            <img src="../uploads/gigs/<?= $gig['image'] ?>" alt="Gig Image" class="w-full h-40 object-cover rounded-md mb-4">
            <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($gig['title']) ?></h2>
            <p class="text-gray-600 mb-2">₹<?= number_format($gig['price'], 2) ?> • <?= $gig['delivery_time'] ?> days</p>
            <div class="flex justify-between">
              <a href="edit_gig.php?id=<?= $gig['id'] ?>" class="text-blue-600 hover:underline">
                <i class="fa-solid fa-pen mr-1"></i> Edit
              </a>
              <a href="delete_gig.php?id=<?= $gig['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this gig?');">
                <i class="fa-solid fa-trash mr-1"></i> Delete
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="text-gray-500">You haven't added any gigs yet.</p>
    <?php endif; ?>
  </div>

</body>
</html>
