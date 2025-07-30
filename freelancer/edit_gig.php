<?php
session_start();
require '../db.php';

// Check if freelancer is logged in
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$freelancer_id = $_SESSION['id'];
$gig_id = $_GET['id'] ?? null;

if (!$gig_id) {
    header("Location: view_gigs.php");
    exit();
}

// Fetch existing gig details
$stmt = $conn->prepare("SELECT * FROM gigs WHERE id = ? AND freelancer_id = ?");
$stmt->bind_param("ii", $gig_id, $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();
$gig = $result->fetch_assoc();

if (!$gig) {
    $_SESSION['gig_message'] = "Gig not found or unauthorized access.";
    header("Location: view_gigs.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);

    // Optional image update
    $imagePath = $gig['image'];
    if ($_FILES['image']['name']) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetDir = "../uploads/gigs/";
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = "uploads/gigs/" . $imageName;
        }
    }

    // Update gig
    $stmt = $conn->prepare("UPDATE gigs SET title = ?, category = ?, description = ?, price = ?, image = ? WHERE id = ? AND freelancer_id = ?");
    $stmt->bind_param("sssssii", $title, $category, $description, $price, $imagePath, $gig_id, $freelancer_id);

    if ($stmt->execute()) {
        $_SESSION['gig_message'] = "Gig updated successfully.";
    } else {
        $_SESSION['gig_message'] = "Error updating gig.";
    }

    header("Location: view_gigs.php");
    exit();
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
  <title>Edit Gig</title>
  <link rel="stylesheet" href="../style/dashboard.css">
</head>
<body>
  <div class="container">
    <h2>Edit Gig</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Title:</label>
      <input type="text" name="title" value="<?= htmlspecialchars($gig['title']) ?>" required>

      <label>Category:</label>
      <input type="text" name="category" value="<?= htmlspecialchars($gig['category']) ?>" required>

      <label>Description:</label>
      <textarea name="description" required><?= htmlspecialchars($gig['description']) ?></textarea>

      <label>Price (₹):</label>
      <input type="number" name="price" value="<?= htmlspecialchars($gig['price']) ?>" required>

      <label>Image (leave blank to keep current):</label>
      <input type="file" name="image">

      <?php if ($gig['image']) : ?>
        <p>Current Image:</p>
        <img src="../<?= $gig['image'] ?>" width="100">
      <?php endif; ?>

      <button type="submit">Update Gig</button>
    </form>
  </div>
</body>
</html>
