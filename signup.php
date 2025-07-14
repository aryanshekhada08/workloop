<?php
session_start();
include 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $_SESSION['auth_type'] = "signup";
    if (empty($name) || empty($email) || empty($password)) {
        $error = "❌ All fields are required.";
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "⚠️ Email is already registered.";
    } else {
        $stmt->close();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = "✅ Account created! You can now log in.";
        } else {
            $error = "❌ Something went wrong. Try again.";
        }
    }

    $stmt->close();
    $conn->close();
}

if (!empty($error)) {
    $_SESSION['auth_error'] = $error;
} else if (!empty($success)) {
    $_SESSION['auth_success'] = $success;
}
header("Location: index.php");
exit();
?>
