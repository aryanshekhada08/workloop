<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Client Dashboard</title>
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
    <main class="flex-1 p-6 max-w-6xl mx-auto">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Welcome, Client 👋</h1>
        <p class="text-gray-600 mb-4">This is your dashboard. From here you can post new job requests, view freelancer offers, and manage your projects.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
          <!-- Card 1 -->
          <div class="bg-blue-100 p-4 rounded-lg shadow-sm">
            <h2 class="text-lg font-medium text-blue-900">Total Requests</h2>
            <p class="text-3xl font-bold text-blue-700 mt-2">3</p>
          </div>
          <!-- Card 2 -->
          <div class="bg-green-100 p-4 rounded-lg shadow-sm">
            <h2 class="text-lg font-medium text-green-900">Offers Received</h2>
            <p class="text-3xl font-bold text-green-700 mt-2">5</p>
          </div>
          <!-- Card 3 -->
          <div class="bg-yellow-100 p-4 rounded-lg shadow-sm">
            <h2 class="text-lg font-medium text-yellow-900">Active Orders</h2>
            <p class="text-3xl font-bold text-yellow-700 mt-2">2</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

</body>
</html>
