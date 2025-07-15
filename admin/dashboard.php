<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#1dbf73",
            graybg: "#f9f9f9"
          }
        }
      }
    }
  </script>
</head>
<body class="bg-graybg font-sans">

<div class="md:flex">

  <!-- ✅ Responsive Sidebar -->
  <?php include 'includes/sidebar.php'; ?>

  <!-- ✅ Main Content -->
  <main class="flex-1 p-4 sm:p-8 md:ml-64 mt-20 md:mt-0">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Welcome, Admin</h1>
      <p class="text-sm text-gray-500">Dashboard Overview</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">👥 Total Users</p>
            <h3 class="text-2xl font-bold mt-2">120</h3>
          </div>
        </div>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">🛠 Total Services</p>
            <h3 class="text-2xl font-bold mt-2">68</h3>
          </div>
        </div>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">📩 Requests</p>
            <h3 class="text-2xl font-bold mt-2">34</h3>
          </div>
        </div>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">📦 Orders</p>
            <h3 class="text-2xl font-bold mt-2">102</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- You can add charts or recent activity below -->

  </main>
</div>

</body>
</html>
