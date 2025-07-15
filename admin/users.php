<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}
include '../db.php';

$users = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Users</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="flex flex-col md:flex-row">
  <!-- Sidebar -->
  <?php include 'includes/sidebar.php'; ?>

  <!-- Main Content -->
  <main class="flex-1 p-4 sm:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">All Users</h1>
      <p class="text-sm text-gray-500">Total: <?= mysqli_num_rows($users) ?></p>
    </div>

    <!-- Responsive Table Wrapper -->
    <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-green-600 text-white text-xs uppercase">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Role</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 divide-y divide-gray-200">
          <?php while ($row = mysqli_fetch_assoc($users)): ?>
          <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-3 font-medium"><?= $row['id'] ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($row['name'] ?? '-') ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($row['email']) ?></td>
            <td class="px-4 py-3 capitalize"><?= $row['role'] ?? 'user' ?></td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="edit_user.php?id=<?= $row['id'] ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs">Edit</a>
              <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Mobile View Fallback: optional card layout -->
    <!-- You can add a card layout here for screens < 640px if preferred -->

  </main>
</div>

</body>
</html>
