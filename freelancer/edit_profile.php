<?php
session_start();
require("../db.php");

// Check if logged in and role is freelancer
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
    $profile_image = $user['profile_image']; // Existing by default

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        if ($_FILES['profile_image']['size'] <= 1048576) { // 1MB limit
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . "." . $ext;
            $upload_path = "../assets/image/user/" . $filename;
            if (!file_exists("../assets/image/user/")) {
                mkdir("../assets/image/user/", 0755, true);
            }
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
            // Refresh user data for redisplay
            $user['name'] = $name;
            $user['phone'] = $phone;
            $user['bio'] = $bio;
            $user['skills'] = $skills;
            $user['location'] = $location;
            $user['profile_image'] = $profile_image;
        } else {
            $message = "Error updating profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Profile - Freelancer</title>
  <script src="https://cdn.tailwindcss.com"></script>
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 min-h-screen flex">

  <!-- Sidebar -->

    <?php include("../components/sidebar.php"); ?>
  

  <div class="flex-1 flex flex-col min-h-screen">

    <!-- Navbar -->
      <?php include("../components/Navbar.php"); ?>
   

    <!-- Main content -->
    <main class="flex-1 overflow-y-auto p-6">
      <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow">

        <h2 class="text-2xl font-bold mb-6 text-center">Edit Your Profile</h2>

        <?php if (!empty($message)) : ?>
          <div class="mb-4 p-3 rounded text-white <?= strpos($message, 'success') !== false ? 'bg-green-500' : 'bg-red-500' ?>">
            <?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
          <div class="flex flex-col items-center space-y-4">
            <img id="profilePreview" src="../assets/image/user/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>" alt="Current Profile" class="w-24 h-24 object-cover rounded-full border border-gray-300">
            <label class="block font-medium">Profile Image (max 1MB)</label>
            <input type="file" name="profile_image" id="profileInput" accept="image/*" class="w-full p-2 border rounded bg-white" />
          </div>
          <div>
            <label class="block mb-1 font-medium">Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full p-2 border rounded" />
          </div>
          <div>
            <label class="block mb-1 font-medium">Email (read-only)</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled class="w-full p-2 border rounded bg-gray-100 cursor-not-allowed" />
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
            <label class="block mb-1 font-medium">Skills (comma-separated)</label>
            <textarea name="skills" rows="2" class="w-full p-2 border rounded"><?= htmlspecialchars($user['skills']) ?></textarea>
          </div>

          <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700 transition">Update Profile</button>
        </form>

      </div>
    </main>

  </div>

<script>
// Image preview on file select
document.getElementById('profileInput').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const preview = document.getElementById('profilePreview');
    preview.src = URL.createObjectURL(file);
  }
});
</script>
</body>
</html>
