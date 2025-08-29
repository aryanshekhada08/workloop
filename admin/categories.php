<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}

include '../db.php';

// Delete category if requested
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
  header("Location: categories.php?deleted=1");
  exit();
}

// Fetch all categories
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY created_at ");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>All Categories</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="md:flex">
  <?php include 'includes/sidebar.php'; ?>

  <main class="flex-1 p-6 md:p-10 md:ml-34 mt-20 md:mt-0">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">📂 Categories</h1>
      <a href="add_category.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">➕ Add Category</a>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">Category deleted successfully.</div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-green-600 text-white">
          <tr>
            <th class="text-left px-4 py-3">ID</th>
            <th class="text-left px-4 py-3">Icon</th>
            <th class="text-left px-4 py-3">Name</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-left px-4 py-3">Created At</th>
            <th class="text-right px-4 py-3">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php while ($row = mysqli_fetch_assoc($categories)): ?>
            <tr class="hover:bg-gray-50 transition">
              <td class="px-4 py-3"><?= $row['id'] ?></td>
              <td class="px-4 py-3">
                <?php if (!empty($row['icon'])): ?>
                  <img src="../<?= $row['icon'] ?>" class="w-8 h-8 object-contain" alt="Icon">
                <?php else: ?>
                  <span class="text-gray-400">N/A</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 font-medium"><?= htmlspecialchars($row['name']) ?></td>
              <td class="px-4 py-3">
                <span class="inline-block px-2 py-1 text-xs font-semibold rounded 
                  <?= $row['active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                  <?= $row['active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td class="px-4 py-3"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
              <td class="px-4 py-3 text-right">
                <!-- Optional: Edit -->
                <a href="edit_category.php?id=<?= $row['id'] ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs">Edit</a>
                <!-- Delete -->
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this category?')"
                   class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

</body>
</html>
