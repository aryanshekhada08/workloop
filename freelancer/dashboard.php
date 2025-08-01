<?php
session_start();
require("../db.php"); // Adjust path as needed

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// $name = $_SESSION['name'] ?? 'Freelancer';

// Fetch data
// Total Gigs
$gigCount = $conn->query("SELECT COUNT(*) FROM gigs WHERE freelancer_id = $user_id")->fetch_row()[0];

// Total Orders
$orderCount = $conn->query("SELECT COUNT(*) FROM orders WHERE freelancer_id = $user_id")->fetch_row()[0];

// Completed Orders
$completedCount = $conn->query("SELECT COUNT(*) FROM orders WHERE freelancer_id = $user_id AND status = 'completed'")->fetch_row()[0];

// Total Earnings
$earningResult = $conn->query("SELECT SUM(amount) FROM orders WHERE freelancer_id = $user_id AND status = 'completed'");
$totalEarnings = $earningResult->fetch_row()[0] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Freelancer Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100">
  
  <?php include("../components/sidebar.php"); ?>
  <?php include("../components/Navbar.php"); ?>


  <div class="ml-64 p-6">
    <h1 class="text-3xl font-bold mb-6">Welcome, <?= $_SESSION['name'] ?> 👋</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

      <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
          <i class="fa-solid fa-briefcase text-[#1DBF73] text-3xl"></i>
          <div>
            <p class="text-gray-500">Total Gigs</p>
            <p class="text-2xl font-bold"><?= $gigCount ?></p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
          <i class="fa-solid fa-box text-[#1DBF73] text-3xl"></i>
          <div>
            <p class="text-gray-500">Total Orders</p>
            <p class="text-2xl font-bold"><?= $orderCount ?></p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
          <i class="fa-solid fa-check-circle text-[#1DBF73] text-3xl"></i>
          <div>
            <p class="text-gray-500">Completed Orders</p>
            <p class="text-2xl font-bold"><?= $completedCount ?></p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
          <i class="fa-solid fa-sack-dollar text-[#1DBF73] text-3xl"></i>
          <div>
            <p class="text-gray-500">Total Earnings</p>
            <p class="text-2xl font-bold">₹<?= number_format($totalEarnings, 2) ?></p>
          </div>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
