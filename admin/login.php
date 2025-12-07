<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db.php'; // Make sure this path is correct and $conn is your mysqli connection

$error = ''; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === '' || $password === '') {
        $error = "Please enter username and password.";
    } else {
        // Prepare and execute SQL query securely
        $stmt = $conn->prepare("SELECT password FROM adminlogin WHERE username = ?");
        if (!$stmt) {
            $error = "Database error: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($main_password);
                $stmt->fetch();
                // Verify password hash
                if ($password == $main_password) {
                    $_SESSION['admin'] = true;  
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid admin credentials!";
                }
            } else {
                $error = "Invalid admin credentials!";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Admin Login</h2>

    <!-- Error message display -->
    <?php if ($error): ?>
      <div class="bg-red-100 text-red-600 text-sm p-3 rounded mb-4">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4" novalidate>
      <div>
        <label class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" required autocomplete="username" autofocus
          class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:outline-none"
          placeholder="Enter admin username">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" required autocomplete="current-password"
          class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:outline-none"
          placeholder="Enter password">
      </div>

      <div>
        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-md transition">Login</button>
      </div>
    </form>
  </div>

</body>
</html>
