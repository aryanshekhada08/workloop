<?php
session_start();
require("../db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch old user data
$stmt = $conn->prepare("SELECT name, email, phone, bio, skills, profile_image, location FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    $skills = trim($_POST['skills']);
    $location = trim($_POST['location']);

    // Handle image upload
    $profile_image = $user['profile_image']; // Default to existing

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        if ($_FILES['profile_image']['size'] <= 1048576) { // 1MB limit
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . "." . $ext;
            $upload_path = "../assets/image/user/" . $filename;
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path);
            $profile_image = $filename;
        } else {
            $message = "Image must be less than 1MB.";
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, bio = ?, skills = ?, location = ?, profile_image = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $name, $phone, $bio, $skills, $location, $profile_image, $user_id);
        if ($stmt->execute()) {
            $message = "Profile updated successfully.";
            $_SESSION['name'] = $name;
            $_SESSION['profile'] = $profile_image;
            // Refresh user data
            $user = ['name' => $name, 'email' => $user['email'], 'phone' => $phone, 'bio' => $bio, 'skills' => $skills, 'location' => $location, 'profile_image' => $profile_image];
        } else {
            $message = "Error updating profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
  <div class="w-full max-w-2xl bg-white p-8 rounded-xl shadow">
    <h2 class="text-2xl font-bold mb-6 text-center">Edit Your Profile</h2>

    <?php if (!empty($message)) : ?>
      <div class="mb-4 p-3 rounded text-white <?= strpos($message, 'success') !== false ? 'bg-green-500' : 'bg-red-500' ?>">
        <?= $message ?>
      </div>
    <?php endif; ?>
 <?php include("../components/sidebar.php"); ?>
  <?php include("../components/Navbar.php"); ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
           <div class="mt-2">
          <img src="../assets/image/user/<?= htmlspecialchars($user['profile_image']) ?>" alt="Current Profile" class="w-20 h-20 object-cover rounded-full">
        </div>
        <label class="block mb-1 font-medium">Profile Image (max 1MB)</label>
        <input type="file" name="profile_image" accept="image/*" class="w-full p-2 border rounded bg-white" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full p-2 border rounded" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Email (read-only)</label>
        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled class="w-full p-2 border rounded bg-gray-100" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="w-full p-2 border rounded" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($user['location']) ?>" class="w-full p-2 border rounded" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Bio</label>
        <textarea name="bio" rows="4" class="w-full p-2 border rounded"><?= htmlspecialchars($user['bio']) ?></textarea>
      </div>
      <div>
        <label class="block mb-1 font-medium">Skills</label>
        <textarea name="skills" rows="2" class="w-full p-2 border rounded"><?= htmlspecialchars($user['skills']) ?></textarea>
      </div>
      
      <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition">Update Profile</button>
    </form>
  </div>
</body>
</html>
