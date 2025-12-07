<?php
session_start();
require 'db.php'; 

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$role = $_POST['role'] ?? '';
$userId = $_SESSION['user_id'];

// Validate input
$allowedRoles = ['freelancer', 'client'];
if (!in_array($role, $allowedRoles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role selected']);
    exit;
}

// Prepare query securely
$stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->bind_param("si", $role, $userId);

if ($stmt->execute()) {
    $_SESSION['role'] = $role;
    $redirect = $role === 'freelancer' ? 'freelancer/dashboard.php' : 'client/dashboard.php';
    echo json_encode(['success' => true, 'redirect' => $redirect]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save role']);
}

$stmt->close();
$conn->close();
