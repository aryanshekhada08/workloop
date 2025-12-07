<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Get role filter from GET, default empty = all
$selectedRole = $_GET['role'] ?? '';

// Prepare base query and parameters for filtering
if ($selectedRole === 'freelancer' || $selectedRole === 'client') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE role = ?");
    $stmt->bind_param("s", $selectedRole);
} else {
    $stmt = $conn->prepare("SELECT * FROM users");
}

$stmt->execute();
$users = $stmt->get_result();
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
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0">
      <h1 class="text-2xl font-bold text-gray-800">All Users</h1>

      <form method="GET" class="flex items-center space-x-2">
        <label for="role" class="font-semibold">Filter by Role:</label>
        <select id="role" name="role" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1">
          <option value="" <?= $selectedRole === '' ? 'selected' : '' ?>>All</option>
          <option value="freelancer" <?= $selectedRole === 'freelancer' ? 'selected' : '' ?>>Freelancer</option>
          <option value="client" <?= $selectedRole === 'client' ? 'selected' : '' ?>>Client</option>
        </select>
      </form>
    </div>

    <p class="text-sm text-gray-500 mb-4">Total: <?= $users->num_rows ?></p>

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
          <?php while ($row = $users->fetch_assoc()): ?>
          <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-3 font-medium"><?= (int)$row['id'] ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($row['name'] ?? '-') ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($row['email']) ?></td>
            <td class="px-4 py-3 capitalize"><?= htmlspecialchars($row['role'] ?? 'user') ?></td>
            <td class="px-4 py-3 text-right space-x-2">
              <a href="edit_user.php?id=<?= $row['id'] ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs">Edit</a>
              <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php if ($users->num_rows === 0): ?>
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">No users found for this filter.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>
</div>

</body>
</html>
