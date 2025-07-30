<?php
session_start();
require '../db.php';

// Check if freelancer is logged in
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

// Check if gig ID is provided
if (isset($_GET['id'])) {
    $gig_id = $_GET['id'];
    $freelancer_id = $_SESSION['id'];

    // Delete only if gig belongs to the logged-in freelancer
    $stmt = $conn->prepare("DELETE FROM gigs WHERE id = ? AND freelancer_id = ?");
    $stmt->bind_param("ii", $gig_id, $freelancer_id);

    if ($stmt->execute()) {
        $_SESSION['gig_message'] = "Gig deleted successfully.";
    } else {
        $_SESSION['gig_message'] = "Error deleting gig.";
    }

    $stmt->close();
}

header("Location: view_gigs.php");
exit();
?>
