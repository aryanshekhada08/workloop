<?php
session_start();
require("../db.php"); // Your DB connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
 <?php include("../components/sidebar.php"); ?>
  <?php include("../components/Navbar.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

  <div class="max-w-4xl mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
    <div class="flex items-center space-x-6">
      <img src="../assets/image/user/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile" class="w-28 h-28 rounded-full object-cover border-4 border-green-500">
      <div>
        <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($user['name']) ?></h2>
        <p class="text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
        <p class="text-sm text-gray-400">Joined: <?= date("d M Y", strtotime($user['created_at'])) ?></p>
      </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-700">Role</h3>
        <p class="text-gray-600"><?= ucfirst($user['role']) ?></p>
      </div>

      <div>
        <h3 class="text-lg font-semibold text-gray-700">Phone</h3>
        <p class="text-gray-600"><?= htmlspecialchars($user['phone'] ?? 'Not added') ?></p>
      </div>

      <div>
        <h3 class="text-lg font-semibold text-gray-700">Location</h3>
        <p class="text-gray-600"><?= htmlspecialchars($user['location'] ?? 'Not added') ?></p>
      </div>

      <div>
        <h3 class="text-lg font-semibold text-gray-700">Rating</h3>
        <p class="text-yellow-500 font-bold"><?= number_format($user['rating'], 1) ?>/5 (<?= $user['total_reviews'] ?> Reviews)</p>
      </div>

      <div class="md:col-span-2">
        <h3 class="text-lg font-semibold text-gray-700">Bio</h3>
        <p class="text-gray-600"><?= nl2br(htmlspecialchars($user['bio'] ?? 'No bio added.')) ?></p>
      </div>

      <div class="md:col-span-2">
        <h3 class="text-lg font-semibold text-gray-700">Skills</h3>
        <p class="text-gray-600"><?= nl2br(htmlspecialchars($user['skills'] ?? 'No skills listed.')) ?></p>
      </div>
    </div>

    <div class="mt-8 text-right">
      <a href="edit_profile.php" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg">Edit Profile</a>
    </div>
  </div>

</body>
</html>
