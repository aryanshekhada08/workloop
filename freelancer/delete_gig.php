<?php
session_start();
require '../db.php';

// Check if freelancer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$freelancer_id = $_SESSION['user_id']; // ✅ use same session key

if (isset($_GET['id'])) {
    $gig_id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM gigs WHERE id = ? AND freelancer_id = ?");
    $stmt->bind_param("ii", $gig_id, $freelancer_id);

    if ($stmt->execute()) {
        $_SESSION['gig_message'] = "✅ Gig deleted successfully.";
    } else {
        $_SESSION['gig_message'] = "❌ Error deleting gig.";
    }

    $stmt->close();
}

header("Location: my_gigs.php");
exit();
?>
