<?php
session_start();
require 'db.php'; 

header('Content-Type: application/json');

$type     = $_POST['auth_type'] ?? '';
$email    = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$response = [];

if ($type === 'signup') {
    $name = trim(mysqli_real_escape_string($conn, $_POST['name'] ?? ''));
    
    if (!$name || !$email || !$password) {
        $response = ['success' => false, 'message' => 'All fields are required.'];
    } else {
        $exists = mysqli_query($conn, "SELECT 1 FROM users WHERE email='$email'");
        if (mysqli_num_rows($exists)) {
            $response = ['success' => false, 'message' => 'Email already exists.'];
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hash')");
            $uid = mysqli_insert_id($conn);

            // Store ID but no role yet
            $_SESSION['user_id'] = $uid;
            $_SESSION['role'] = '';

            $response = [
                'success' => true,
                'show_role_modal' => true,
                'user_id' => $uid
            ];
        }
    }
} else { // LOGIN
    if (!$email || !$password) {
        $response = ['success' => false, 'message' => 'Email and password are required.'];
    } else {
        $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if ($user = mysqli_fetch_assoc($res)) {
            if (password_verify($password, $user['password'])) {
                // Valid password
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Role must exist to redirect
                if ($user['role'] === 'freelancer') {
                    $response = ['success' => true, 'redirect' => 'freelancer/dashboard.php'];
                } elseif ($user['role'] === 'client') {
                    $response = ['success' => true, 'redirect' => 'client/dashboard.php'];
                } else {
                    $response = ['success' => false, 'message' => 'Your account does not have a role. Contact admin.'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Invalid password.'];
            }
        } else {
            $response = ['success' => false, 'message' => 'User not found.'];
        }
    }
}

echo json_encode($response);
