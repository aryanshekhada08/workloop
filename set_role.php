<?php
session_start();
require 'db.php'; // adjust if needed

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$role = $_POST['role'];
$userId = $_SESSION['user_id'];

// Validate role input
if ($role !== 'freelancer' && $role !== 'client') {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

$update = mysqli_query($conn, "UPDATE users SET role='$role' WHERE id=$userId");

if ($update) {
    $_SESSION['role'] = $role;
    $redirect = $role === 'freelancer' ? 'freelancer/dashboard.php' : 'client/dashboard.php';
    echo json_encode(['success' => true, 'redirect' => $redirect]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save role']);
}
