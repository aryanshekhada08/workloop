<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Freelancer Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<?php include '../components/Navbar.php'; ?>

<div class="flex">
  <!-- Sidebar -->
  <aside class="w-64 bg-white h-screen shadow-md p-4">
    <h2 class="text-xl font-bold mb-4">Freelancer Menu</h2>
    <ul class="space-y-2">
      <li><a href="#" class="block p-2 rounded hover:bg-gray-200">Browse Requests</a></li>
      <li><a href="#" class="block p-2 rounded hover:bg-gray-200">My Offers</a></li>
      <li><a href="#" class="block p-2 rounded hover:bg-gray-200">Accepted Projects</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6">
    <h1 class="text-2xl font-semibold mb-4">Welcome, Freelancer!</h1>
    <p>This is your dashboard. You can browse job requests and manage offers here.</p>
  </main>
</div>

</body>
</html>
