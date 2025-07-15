<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit();
}

include '../db.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  // Optional: prevent deleting the currently logged-in admin
  if ($_SESSION['admin'] == $id) {
    echo "You cannot delete your own admin account.";
    exit();
  }

  // Delete the user
  $query = "DELETE FROM users WHERE id = $id";
  if (mysqli_query($conn, $query)) {
    header("Location: users.php?deleted=1");
  } else {
    echo "Failed to delete user.";
  }
} else {
  header("Location: users.php");
}
?>
