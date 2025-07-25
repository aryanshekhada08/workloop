<?php
session_start();
require 'db.php'; 

// Sanitize inputs
$type = $_POST['auth_type'] ?? '';
$email = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$response = [];  

if ($type === 'signup') {
    $name = trim(mysqli_real_escape_string($conn, $_POST['name'] ?? ''));
    
    if (!$name || !$email || !$password) {
        $response = ['success' => false, 'message' => 'All fields are required.'];
    } else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $response = ['success' => false, 'message' => 'Email already exists.'];
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed')");
            if ($insert) {
                $userId = mysqli_insert_id($conn);
                $_SESSION['user_id'] = $userId;
                $_SESSION['role'] = ''; 
                $response = ['success' => true, 'showRoleModal' => true, 'userId' => $userId];
            } else {
                $response = ['success' => false, 'message' => 'Signup failed.'];
            }
        }
    }

} else { // login
    if (!$email || !$password) {
        $response = ['success' => false, 'message' => 'Email and password required.'];
    } else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if ($row = mysqli_fetch_assoc($check)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];

                if (empty($row['role'])) {
                    $response = ['success' => true, 'showRoleModal' => true, 'userId' => $row['id']];
                } else {
                    $redirect = $row['role'] === 'freelancer' ? 'freelancer/dashboard.php' : 'client/dashboard.php';
                    $response = ['success' => true, 'redirect' => $redirect];
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
