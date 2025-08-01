<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $delivery_time = intval($_POST['delivery_time']);
    $category = trim($_POST['category']);
    $image = $_FILES['image'];

    $image = $_FILES['image'];

if ($title && $description && $price && $delivery_time && $category && $image['name']) {
    // Validate image size (max 1MB = 1048576 bytes)
    if ($image['size'] > 1048576) {
        $message = "Image must be less than 1MB.";
    } else {
        $uploadDir = "assets/image/uploads/gigs/";
        $imgName = time() . "_" . basename($image['name']);
        $targetPath = $uploadDir . $imgName;

        // Ensure directory exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Upload the image
        if (move_uploaded_file($image['tmp_name'], $targetPath)) {
            $freelancer_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO gigs (freelancer_id, title, description, price, delivery_time, category, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdiss", $freelancer_id, $title, $description, $price, $delivery_time, $category, $imgName);

            if ($stmt->execute()) {
                $message = "Gig added successfully!";
            } else {
                $message = "Error saving gig to database.";
            }
        } else {
            $message = "Failed to upload image.";
        }
    }
} else {
    $message = "Please fill in all required fields.";
}

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Gig</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100">

  <?php include("../components/sidebar.php"); ?>
  <?php include("../components/Navbar.php"); ?>


  <div class="ml-80 p-6">
    <h1 class="text-3xl font-bold mb-6 ">Add New Gig</h1>

    <?php if ($message): ?>
      <div class="mb-4 text-white p-3 rounded <?= strpos($message, 'success') !== false ? 'bg-green-500' : 'bg-red-500' ?>">
        <?= $message ?>
      </div>
    <?php endif; ?>

    <form method="POST"  action=""  enctype="multipart/form-data" class="bg-white p-9 ml-44 rounded shadow-md max-w-3xl">
      <div class="mb-4">
        <label class="block mb-1 font-medium">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" required class="w-full border border-gray-300 p-2 rounded">
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Description <span class="text-red-500">*</span></label>
        <textarea name="description" rows="4" required class="w-full border border-gray-300 p-2 rounded"></textarea>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Price (INR) <span class="text-red-500">*</span></label>
        <input type="number" name="price" min="100" required class="w-full border border-gray-300 p-2 rounded">
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Delivery Time (Days) <span class="text-red-500">*</span></label>
        <input type="number" name="delivery_time" min="1" required class="w-full border border-gray-300 p-2 rounded">
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Category</label>
        <input type="text" name="category" placeholder="e.g. Logo Design, Web Development" class="w-full border border-gray-300 p-2 rounded">
      </div>

      <div class="mb-6">
        <label class="block mb-1 font-medium">Upload Gig Image <span class="text-red-500">*</span></label>
        <input type="file" name="image" accept="image/*" required class="w-full">
      </div>

      <button type="submit" class="bg-[#1DBF73] text-white px-4 py-2 rounded hover:bg-green-600 transition">
        <i class="fa-solid fa-plus mr-2"></i> Add Gig
      </button>
    </form>
  </div>

</body>
</html>
