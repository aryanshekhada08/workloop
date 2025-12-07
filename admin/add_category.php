<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}

include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $active = isset($_POST['active']) ? 1 : 0;

  // Handle image upload
  $iconPath = '';
  if (isset($_FILES['icon']) && $_FILES['icon']['error'] === 0) {
    $targetDir = '../assets/image/cat_icons/';
    $filename = basename($_FILES['icon']['name']);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $newName = uniqid('cat_', true) . '.' . $ext;
    $targetFile = $targetDir . $newName;

    // Allowed file types
    $allowed = ['png', 'jpg', 'jpeg', 'svg'];
    if (in_array(strtolower($ext), $allowed)) {
      if (move_uploaded_file($_FILES['icon']['tmp_name'], $targetFile)) {
        $iconPath = '/assets/image/cat_icons/' . $newName; // path saved to DB
      } else {
        $error = "Failed to upload icon.";
      }
    } else {
      $error = "Invalid file type.";
    }
  }

  if (!empty($name) && $iconPath) {
    $query = "INSERT INTO categories (name, icon, active) VALUES ('$name', '$iconPath', $active)";
    mysqli_query($conn, $query);
    header("Location: categories.php?added=1");
    exit();
  } else if (!$iconPath && empty($error)) {
    $error = "Icon upload required.";
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Category</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<div class="md:flex">
  <?php include 'includes/sidebar.php'; ?>

  <main class="flex-1 p-4 md:p-10 md:ml-64 mt-20 md:mt-0">

    <div class="max-w-xl bg-white p-6 rounded-xl ml-6 shadow mx-auto">
      <h1 class="text-2xl font-bold mb-4 text-gray-800">Add New Category</h1>

      <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4"><?= $error ?></div>
      <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Category Name</label>
          <input type="text" name="name" placeholder="Enter category name" required
            class="w-full mt-1 px-4 py-2 border rounded shadow-sm focus:ring-green-500 focus:border-green-500" />
        </div>

        <div>
  <label class="block text-sm font-medium text-gray-700">Upload Icon</label>
  <input type="file" name="icon" accept=".png,.jpg,.jpeg,.svg"
    class="w-full mt-1 px-3 py-2 border rounded shadow-sm bg-white" required />
</div>

        <div class="flex items-center space-x-2">
          <input type="checkbox" name="active" class="form-checkbox text-green-600" checked />
          <span class="text-sm text-gray-700">Make Active</span>
        </div>

        <div class="flex justify-between items-center pt-4">
          <a href="categories.php" class="text-sm text-gray-600 hover:underline">← Back to Categories</a>
          <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition">
            Add Category
          </button>
        </div>
      </form>
    </div>
  </main>
</div>

</body>
</html>
