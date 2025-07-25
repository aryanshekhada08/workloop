<?php
session_start();
require 'db.php'; 

header('Content-Type: application/json');

// 1) If already logged in but role empty
if (isset($_SESSION['user_id']) && empty($_SESSION['role'])) {
    echo json_encode([
      'success'         => true,
      'show_role_modal' => true,
      'user_id'         => $_SESSION['user_id']
    ]);
    exit;
}

// Grab & sanitize
$type     = $_POST['auth_type'] ?? '';
$email    = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$response = [];

if ($type === 'signup') {
    $name = trim(mysqli_real_escape_string($conn, $_POST['name'] ?? ''));
    if (!$name || !$email || !$password) {
        $response = ['success'=>false,'message'=>'All fields are required.'];
    } else {
        $exists = mysqli_query($conn, "SELECT 1 FROM users WHERE email='$email'");
        if (mysqli_num_rows($exists)) {
            $response = ['success'=>false,'message'=>'Email already exists.'];
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (name,email,password) VALUES ('$name','$email','$hash')");
            $uid = mysqli_insert_id($conn);
            $_SESSION['user_id'] = $uid;
            $_SESSION['role']    = '';
            $response = [
              'success'         => true,
              'show_role_modal' => true,
              'user_id'         => $uid
            ];
        }
    }
} else { // login
    if (!$email || !$password) {
        $response = ['success'=>false,'message'=>'Email and password required.'];
    } else {
        $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if ($user = mysqli_fetch_assoc($res)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role']    = $user['role'];

                if (empty($user['role'])) {
                    $response = [
                      'success'         => true,
                      'show_role_modal' => true,
                      'user_id'         => $user['id']
                    ];
                } else {
                    $redir = $user['role']==='freelancer'
                             ? 'freelancer/dashboard.php'
                             : 'client/dashboard.php';
                    $response = ['success'=>true,'redirect'=>$redir];
                }
            } else {
                $response = ['success'=>false,'message'=>'Invalid password.'];
            }
        } else {
            $response = ['success'=>false,'message'=>'User not found.'];
        }
    }
}

echo json_encode($response);
