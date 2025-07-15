<?php
session_start();
include '../db.php'; // Your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Hardcoded admin check (you can change to DB later)
  if ($username === 'admin' && $password === 'admin123') {
    $_SESSION['admin'] = true;
    header("Location: dashboard.php");
    exit();
  } else {
    $error = "Invalid admin credentials!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Admin Login</h2>

    <?php if (isset($error)): ?>
      <div class="bg-red-100 text-red-600 text-sm p-3 rounded mb-4">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" required class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:outline-none" placeholder="Enter admin username">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" required class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:outline-none" placeholder="Enter password">
      </div>

      <div>
        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-md transition">Login</button>
      </div>
    </form>
  </div>

</body>
</html>
