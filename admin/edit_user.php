<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}

include '../db.php';

if (!isset($_GET['id'])) {
  header("Location: users.php");
  exit();
}

$id = intval($_GET['id']);
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));

if (!$user) {
  echo "User not found.";
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $role = mysqli_real_escape_string($conn, $_POST['role']);

  mysqli_query($conn, "UPDATE users SET name='$name', email='$email', role='$role' WHERE id=$id");

  header("Location: users.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="transition duration-300" id="htmlRoot">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit User</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<div class="md:flex">
  <?php include 'includes/sidebar.php'; ?>

  <main class="flex-1 p-4 md:p-10 md:ml-64 mt-20 md:mt-0">
    <div class="max-w-xl bg-white p-6 rounded-xl shadow mx-auto">
      <h1 class="text-2xl font-bold mb-6 text-gray-800">Edit User</h1>

      <form method="POST" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full mt-1 px-4 py-2 border rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full mt-1 px-4 py-2 border rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Role</label>
          <select name="role" class="w-full mt-1 px-4 py-2 border rounded-md focus:ring-green-500 focus:border-green-500">
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>

        <div class="flex justify-between items-center pt-4">
          <a href="users.php" class="text-sm text-gray-600 hover:underline">← Back to Users</a>
          <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition">Update</button>
        </div>
      </form>
    </div>
  </main>
</div>

</body>
</html>
