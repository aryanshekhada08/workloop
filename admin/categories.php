<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}

include '../db.php';

// Add category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $icon = mysqli_real_escape_string($conn, $_POST['icon']);
  $active = isset($_POST['active']) ? 1 : 0;

  if (!empty($name)) {
    $query = "INSERT INTO categories (name, icon, active) VALUES ('$name', '$icon', $active)";
    mysqli_query($conn, $query);
    header("Location: categories.php?success=1");
    exit();
  }
}

// Delete category
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
  header("Location: categories.php?deleted=1");
  exit();
}

// Fetch categories
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
  <div class="md:flex">
    <?php include 'includes/sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-10 md:ml-64 mt-20 md:mt-0">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">Manage Categories</h1>

      <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">Category added successfully.</div>
      <?php elseif (isset($_GET['deleted'])): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">Category deleted.</div>
      <?php endif; ?>

      <!-- Add Category Form -->
      <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 bg-white p-6 rounded shadow">
        <input type="text" name="name" placeholder="Category Name"
          class="px-4 py-2 border rounded shadow-sm" required>

        <input type="text" name="icon" placeholder="Icon Path or SVG"
          class="px-4 py-2 border rounded shadow-sm">

        <label class="flex items-center space-x-2">
          <input type="checkbox" name="active" class="form-checkbox rounded text-green-600" checked>
          <span class="text-sm">Active</span>
        </label>

        <div class="md:col-span-3">
          <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded">Add Category</button>
        </div>
      </form>

      <!-- Category Table -->
      <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-green-600 text-white">
            <tr>
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Icon</th>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Active</th>
              <th class="px-4 py-3 text-left">Created</th>
              <th class="px-4 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php while ($row = mysqli_fetch_assoc($categories)): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3"><?= $row['id'] ?></td>
                <td class="px-4 py-3">
                  <?= $row['icon'] ? "<img src='{$row['icon']}' class='h-6 w-6'>" : '-' ?>
                </td>
                <td class="px-4 py-3"><?= htmlspecialchars($row['name']) ?></td>
                <td class="px-4 py-3">
                  <span class="inline-block px-2 py-1 rounded text-xs font-medium 
                    <?= $row['active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= $row['active'] ? 'Active' : 'Inactive' ?>
                  </span>
                </td>
                <td class="px-4 py-3"><?= $row['created_at'] ?></td>
                <td class="px-4 py-3 text-right">
                  <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this category?')"
                    class="text-red-500 hover:underline text-sm">Delete</a>
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
