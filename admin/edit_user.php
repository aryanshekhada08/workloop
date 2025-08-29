<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: users.php");
    exit();
}

$message = "";

// Handle main user info update POST action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;

    if ($name === '' || $email === '') {
        $message = "Name and email are required.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, is_verified = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $email, $phone, $is_verified, $id);
        if ($stmt->execute()) {
            $message = "User updated successfully.";
        } else {
            $message = "Failed to update user.";
        }
        $stmt->close();
    }
}

// Fetch latest user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit User - <?= htmlspecialchars($user['name']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

  <div class="max-w-lg mx-auto bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Edit User: <?= htmlspecialchars($user['name']) ?></h1>

    <?php if ($message): ?>
    <div class="mb-4 p-3 rounded <?= strpos($message, 'successfully') !== false ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label class="block font-semibold mb-1" for="name">Name <span class="text-red-500">*</span></label>
        <input id="name" name="name" type="text" required class="w-full border border-gray-300 rounded px-3 py-2" value="<?= htmlspecialchars($user['name']) ?>" />
      </div>

      <div>
        <label class="block font-semibold mb-1" for="email">Email <span class="text-red-500">*</span></label>
        <input id="email" name="email" type="email" required class="w-full border border-gray-300 rounded px-3 py-2" value="<?= htmlspecialchars($user['email']) ?>" />
      </div>

      <div>
        <label class="block font-semibold mb-1" for="phone">Phone</label>
        <input id="phone" name="phone" type="text" class="w-full border border-gray-300 rounded px-3 py-2" value="<?= htmlspecialchars($user['phone']) ?>" />
      </div>

      <div class="flex items-center space-x-2">
        <input type="checkbox" id="is_verified" name="is_verified" <?= $user['is_verified'] ? 'checked' : '' ?> class="form-checkbox h-5 w-5 text-green-600" />
        <label for="is_verified" class="font-semibold select-none">Is Verified</label>
      </div>

      <div class="flex justify-between items-center">
        <a href="users.php" class="text-gray-600 hover:underline">&larr; Back to Users</a>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded">Update User</button>
      </div>
    </form>

    <a href="delete_user.php?id=<?= $user['id'] ?>" 
      class="mt-6 inline-block px-4 py-2 rounded bg-red-700 hover:bg-red-800 text-white font-semibold"
      onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
      Delete User
    </a>

  </div>

</body>
</html>
