<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gig_id = intval($_POST['gig_id']);
    $client_id = intval($_POST['client_id']);
    $freelancer_id = intval($_POST['freelancer_id']);
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    // Basic validation
    if ($gig_id <= 0 || $client_id <= 0 || $freelancer_id <= 0 || $rating < 1 || $rating > 5 || empty($review_text)) {
        echo "Invalid input.";
        exit();
    }

    // Check if review already exists to prevent duplicates
    $stmtCheck = $conn->prepare("SELECT id FROM reviews WHERE gig_id = ? AND client_id = ?");
    $stmtCheck->bind_param("ii", $gig_id, $client_id);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        echo "You have already submitted a review for this gig.";
        exit();
    }
    $stmtCheck->close();

    // Insert review
    $stmtInsert = $conn->prepare("INSERT INTO reviews (gig_id, freelancer_id, client_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmtInsert->bind_param("iiiis", $gig_id, $freelancer_id, $client_id, $rating, $review_text);

    if ($stmtInsert->execute()) {
        header("Location: order_details.php?id=" . $_POST['order_id'] ?? 0 . "&review_submitted=1");
        exit();
    } else {
        echo "Failed to submit review. Please try again.";
    }
    $stmtInsert->close();
}

$conn->close();
?>
