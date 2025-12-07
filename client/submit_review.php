<?php
session_start();
require("../db.php");

// Check if client is logged in and authorized
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Get POST inputs and sanitize
$gig_id = intval($_POST['gig_id'] ?? 0);
$freelancer_id = intval($_POST['freelancer_id'] ?? 0);
$order_id = intval($_POST['order_id'] ?? 0);
$rating = intval($_POST['rating'] ?? 0);
$review_text = trim($_POST['review_text'] ?? '');

// Basic validations
$errors = [];
if ($gig_id <= 0) $errors[] = "Invalid gig ID.";
if ($freelancer_id <= 0) $errors[] = "Invalid freelancer ID.";
if ($order_id <= 0) $errors[] = "Invalid order ID.";
if ($rating < 1 || $rating > 5) $errors[] = "Rating must be between 1 and 5.";
if (strlen($review_text) === 0) $errors[] = "Review text cannot be empty.";

// If any errors, show and exit
if ($errors) {
    echo "<h2>Errors:</h2><ul>";
    foreach ($errors as $err) {
        echo "<li>" . htmlspecialchars($err) . "</li>";
    }
    echo "</ul><a href='order_details.php?id={$order_id}'>Go back</a>";
    exit();
}

// Optional: Check if client already submitted a review for this order or gig
// $stmt = $conn->prepare("SELECT id FROM reviews WHERE client_id = ? AND order_id = ? LIMIT 1");
$stmt = $conn->prepare("SELECT id FROM reviews WHERE client_id = ? AND gig_id = ? LIMIT 1");

$stmt->bind_param("ii", $client_id, $order_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    die("You have already submitted a review for this order.");
}
$stmt->close();

// Insert the review
$stmt = $conn->prepare("INSERT INTO reviews (gig_id, client_id, freelancer_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iiiss", $gig_id, $client_id, $freelancer_id, $rating, $review_text);
if (!$stmt->execute()) {
    die("Failed to submit review: " . $stmt->error);
}
$stmt->close();

// Update freelancer's rating and total_reviews
$stmt = $conn->prepare("SELECT rating, total_reviews FROM users WHERE id = ?");
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$currentRating = floatval($user['rating'] ?? 0);
$currentTotalReviews = intval($user['total_reviews'] ?? 0);

$newAverage = (($currentRating * $currentTotalReviews) + $rating) / ($currentTotalReviews + 1);
$newTotalReviews = $currentTotalReviews + 1;

$stmt = $conn->prepare("UPDATE users SET rating = ?, total_reviews = ? WHERE id = ?");
$stmt->bind_param("dii", $newAverage, $newTotalReviews, $freelancer_id);

if (!$stmt->execute()) {
    die("Failed to update freelancer rating: " . $stmt->error);
}
$stmt->close();

// Redirect back to order details with success message
header("Location: order_details.php?id={$order_id}&review_submitted=1");
exit();
?>
