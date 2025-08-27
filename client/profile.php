<?php
session_start();
require("../db.php");

// Ensure logged in and role is client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$error = "";

// Fetch current client data
$stmt = $conn->prepare("SELECT name, email, phone, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);

    $profile_image = $user['profile_image'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        if ($_FILES['profile_image']['size'] <= 1048576) { // 1MB limit
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . "." . $ext;
            $upload_path = "../assets/image/user/" . $filename;
            if (!file_exists("../assets/image/user/")) {
                mkdir("../assets/image/user/", 0755, true);
            }
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $profile_image = $filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Image must be less than 1MB.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, profile_image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $phone, $profile_image, $user_id);
        if ($stmt->execute()) {
            $message = "Profile updated successfully.";
            $user['name'] = $name;
            $user['phone'] = $phone;
            $user['profile_image'] = $profile_image;
        } else {
            $error = "Error updating profile.";
        }
        $stmt->close();
    }
}

function initials($name) {
    $words = explode(" ", $name);
    $initials = "";
    foreach ($words as $w) {
        if (strlen($w) > 0) $initials .= strtoupper($w[0]);
    }
    return substr($initials, 0, 2);
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <title>Client Profile | WorkLoop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <style>
       #editProfileForm { display: none; }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex">
    <?php include("../components/sidebar.php"); ?>

    <div class="flex-1 flex flex-col min-h-screen">
        <?php include("../components/Navbar.php"); ?>

        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow">

                <h2 class="text-2xl font-bold mb-4 text-center text-green-700 select-none">Client Profile</h2>

                <?php if ($message): ?>
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-800"><?= htmlspecialchars($message) ?></div>
                <?php elseif ($error): ?>
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-800"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <!-- View Mode -->
                <div id="viewProfile">
                    <div class="flex flex-col md:flex-row items-center mb-6 space-y-4 md:space-y-0 md:space-x-6">
                        <?php if (!empty($user['profile_image']) && file_exists("../assets/image/user/" . $user['profile_image'])): ?>
                            <img src="../assets/image/user/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="w-24 h-24 rounded-full object-cover" />
                        <?php else: ?>
                            <div class="w-24 h-24 rounded-full bg-green-700 flex items-center justify-center text-white font-bold text-4xl select-none">
                                <?= initials($user['name']) ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <h3 class="text-xl font-semibold"><?= htmlspecialchars($user['name']) ?></h3>
                            <p class="text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                            <p class="mt-2"><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?: 'N/A') ?></p>
                        </div>
                    </div>

                    <button id="editBtn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded transition">Edit Profile</button>
                </div>

                <!-- Edit Mode -->
                <form id="editProfileForm" method="POST" enctype="multipart/form-data" class="space-y-6">

                    <div class="flex flex-col items-center space-y-4">
                        <?php if (!empty($user['profile_image']) && file_exists("../assets/image/user/" . $user['profile_image'])): ?>
                            <img id="profilePreview" src="../assets/image/user/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="w-24 h-24 rounded-full object-cover border border-gray-300" />
                        <?php else: ?>
                            <div id="profilePreviewFallback" class="w-24 h-24 rounded-full bg-green-700 flex items-center justify-center text-white font-bold text-4xl select-none">
                                <?= initials($user['name']) ?>
                            </div>
                        <?php endif; ?>
                        <label class="block cursor-pointer text-green-700 hover:text-green-900">
                            Change Photo
                            <input type="file" id="profileInput" name="profile_image" accept="image/*" class="hidden" />
                        </label>
                    </div>

                    <label class="block font-medium">Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                    <label class="block font-medium">Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />


                    <div class="flex space-x-4">
                        <button type="submit" name="update_profile" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded transition">Save Changes</button>
                        <button type="button" id="cancelBtn" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded transition">Cancel</button>
                    </div>
                </form>

            </div>
        </main>
    </div>

<script>
// Toggle between view and edit
document.getElementById('editBtn').addEventListener('click', () => {
    document.getElementById('viewProfile').style.display = 'none';
    document.getElementById('editProfileForm').style.display = 'block';
});
document.getElementById('cancelBtn').addEventListener('click', () => {
    document.getElementById('editProfileForm').style.display = 'none';
    document.getElementById('viewProfile').style.display = 'block';
});

// Image preview on file change
document.getElementById('profileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('profilePreview');
    const fallback = document.getElementById('profilePreviewFallback');
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        if (fallback) fallback.style.display = 'none';
    }
});
</script>

</body>
</html>
