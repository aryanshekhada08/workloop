<?php
session_start();
require("../db.php");

// Ensure logged in and role is client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user info
$stmt = $conn->prepare("SELECT name, email, phone, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $hashed_password);
$stmt->fetch();
$stmt->close();

$message = "";
$error = "";

// Handle profile update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_phone = trim($_POST['phone']);

    if ($new_name && $new_email) {
        $update_stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
        $update_stmt->bind_param("sssi", $new_name, $new_email, $new_phone, $user_id);
        if ($update_stmt->execute()) {
            $message = "Profile updated successfully.";
            $name = $new_name;
            $email = $new_email;
            $phone = $new_phone;
        } else {
            $error = "Error updating profile.";
        }
        $update_stmt->close();
    } else {
        $error = "Name and Email are required.";
    }
}

// Handle password change POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$current_password || !$new_password || !$confirm_password) {
        $error = "All password fields are required.";
    } elseif (!password_verify($current_password, $hashed_password)) {
        $error = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $pass_stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $pass_stmt->bind_param("si", $new_hashed, $user_id);
        if ($pass_stmt->execute()) {
            $message = "Password changed successfully.";
        } else {
            $error = "Error updating password.";
        }
        $pass_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <title>Settings - Client | WorkLoop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <?php include '../components/Navbar.php'; ?>
    <div class="flex flex-1 overflow-hidden">
        <?php include '../components/sidebar.php'; ?>

        <main class="flex-grow max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md my-6">

            <h1 class="text-3xl font-semibold mb-6 text-green-700 select-none">Settings</h1>

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

            <form method="post" class="space-y-10 max-w-lg">

                <section>
                    <h2 class="text-xl font-semibold mb-4">Profile Details</h2>

                    <label class="block mb-1 font-medium">Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                    <label class="block mt-4 mb-1 font-medium">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                    <label class="block mt-4 mb-1 font-medium">Phone</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                    <button type="submit" name="update_profile"
                        class="mt-6 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition">Update Profile</button>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-4">Change Password</h2>

                    <label class="block mb-1 font-medium">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                    <label class="block mt-4 mb-1 font-medium">New Password</label>
                    <input type="password" name="new_password" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                    <label class="block mt-4 mb-1 font-medium">Confirm New Password</label>
                    <input type="password" name="confirm_password" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700" />

                    <button type="submit" name="change_password"
                        class="mt-6 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition">Change Password</button>
                </section>

            </form>

        </main>
    </div>

</body>

</html>
