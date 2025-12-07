<?php
session_start();
require("../db.php");

// Ensure user is logged in (adjust role check if needed)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT name, email, phone, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $profile_image);
$stmt->fetch();
$stmt->close();

$message = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_phone = trim($_POST['phone']);

    // Validate fields
    if (!$new_name || !$new_email) {
        $error = "Name and Email are required.";
    } else {
        // Handle image upload if there is a file
        if (!empty($_FILES['profile_image']['name'])) {
            $target_dir = "../uploads/profiles/";
            $file_tmp = $_FILES['profile_image']['tmp_name'];
            $file_name = basename($_FILES['profile_image']['name']);
            $target_file = $target_dir . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($imageFileType, $allowed_types)) {
                $error = "Only JPG, JPEG, PNG & GIF files are allowed for the profile image.";
            } elseif (!move_uploaded_file($file_tmp, $target_file)) {
                $error = "Failed to upload profile image.";
            } else {
                $profile_image = $file_name; // Update profile image filename to save in DB
            }
        }

        if (!$error) {
            // Update user info in database
            $update_stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, profile_image=? WHERE id=?");
            $update_stmt->bind_param("ssssi", $new_name, $new_email, $new_phone, $profile_image, $user_id);
            if ($update_stmt->execute()) {
                $message = "Profile updated successfully.";

                // Reflect changes immediately
                $name = $new_name;
                $email = $new_email;
                $phone = $new_phone;
            } else {
                $error = "Error updating profile.";
            }
            $update_stmt->close();
        }
    }
}

// Helper function: get initials from name
function initials($name) {
    $words = explode(" ", $name);
    $initials = "";
    foreach ($words as $w) {
        if (strlen($w) > 0) {
            $initials .= strtoupper($w[0]);
        }
    }
    return substr($initials, 0, 2);
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <title>Update Profile | WorkLoop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <?php include '../components/Navbar.php'; ?>
    <div class="flex flex-1 overflow-hidden">
        <?php include '../components/sidebar.php'; ?>

        <main class="flex-grow max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md my-6">

            <h1 class="text-3xl font-semibold mb-6 text-green-700 select-none">Update Profile</h1>

            <?php if ($message): ?>
                <div class="mb-4 py-2 px-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-4 py-2 px-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="space-y-6">

                <div class="flex items-center space-x-6">
                    <?php if ($profile_image && file_exists("../uploads/profiles/" . $profile_image)): ?>
                        <img src="../uploads/profiles/<?= htmlspecialchars($profile_image) ?>" alt="Profile Image"
                             class="h-20 w-20 rounded-full object-cover" />
                    <?php else: ?>
                        <div class="h-20 w-20 rounded-full bg-green-700 flex items-center justify-center text-white font-bold text-3xl select-none">
                            <?= initials($name) ?>
                        </div>
                    <?php endif; ?>

                    <label class="block cursor-pointer text-green-700 hover:text-green-900">
                        Change Photo
                        <input type="file" name="profile_image" accept="image/*" class="hidden" />
                    </label>
                </div>

                <label class="block font-medium">Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required
                       class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                <label class="block font-medium">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required
                       class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                <label class="block font-medium">Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>"
                       class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                <button type="submit" name="update_profile"
                        class="mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition">
                    Update Profile
                </button>
            </form>
        </main>
    </div>
</body>
</html>
