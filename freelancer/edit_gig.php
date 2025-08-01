<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$gig_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];
$message = '';

$stmt = $conn->prepare("SELECT * FROM gigs WHERE id = ? AND freelancer_id = ?");
$stmt->bind_param("ii", $gig_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$gig = $result->fetch_assoc();

if (!$gig) {
    echo "Gig not found or unauthorized access.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $delivery_time = intval($_POST['delivery_time']);
    $category = trim($_POST['category']);
    $imgName = $gig['image'];

    if (!empty($_FILES['image']['name'])) {
        if ($_FILES['image']['size'] > 1048576) {
            $message = "Image must be less than 1MB.";
        } else {
            $imgName = time() . "_" . basename($_FILES['image']['name']);
            $target = "../assets/image/uploads/gigs/" . $imgName;
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("UPDATE gigs SET title=?, description=?, price=?, delivery_time=?, category=?, image=? WHERE id=? AND freelancer_id=?");
        $stmt->bind_param("ssdiissi", $title, $description, $price, $delivery_time, $category, $imgName, $gig_id, $user_id);
        if ($stmt->execute()) {
            $message = "Gig updated successfully!";
            $gig = array_merge($gig, [
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'delivery_time' => $delivery_time,
                'category' => $category,
                'image' => $imgName
            ]);
        } else {
            $message = "Failed to update gig.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Gig</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <?php include("../components/sidebar.php"); ?>
  <?php include("../components/Navbar.php"); ?>

  <div class="ml-80 p-6 ">
    <h1 class="text-3xl font-bold mb-6">Edit Gig</h1>

    <?php if ($message): ?>
      <div class="mb-4 text-white p-3 rounded <?= strpos($message, 'success') !== false ? 'bg-green-500' : 'bg-red-500' ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-9 rounded shadow-md max-w-3xl">
      <div class="mb-4">
        <label class="block mb-1 font-medium">Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($gig['title']) ?>" required class="w-full border border-gray-300 p-2 rounded" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Description</label>
        <textarea name="description" rows="4" required class="w-full border border-gray-300 p-2 rounded"><?= htmlspecialchars($gig['description']) ?></textarea>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Price (INR)</label>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($gig['price']) ?>" required class="w-full border border-gray-300 p-2 rounded" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Delivery Time (Days)</label>
        <input type="number" name="delivery_time" value="<?= htmlspecialchars($gig['delivery_time']) ?>" required class="w-full border border-gray-300 p-2 rounded" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Category</label>
        <input type="text" name="category" value="<?= htmlspecialchars($gig['category']) ?>" class="w-full border border-gray-300 p-2 rounded" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Upload New Image (optional)</label>
        <input type="file" name="image" accept="image/*" onchange="validateFileSize(this)" class="w-full" />
      </div>

      <div class="mb-6 flex gap-4 items-center">
        <span class="text-sm text-gray-600">Current Image:</span>
        <img src="../assets/image/uploads/gigs/<?= htmlspecialchars($gig['image']) ?>" class="h-20 border rounded" />
      </div>

      <button type="submit" class="bg-[#1DBF73] text-white px-4 py-2 rounded hover:bg-green-600 transition">
        <i class="fa-solid fa-save mr-2"></i> Update Gig
      </button>
    </form>
  </div>

  <script>
    function validateFileSize(input) {
      const file = input.files[0];
      if (file && file.size > 1048576) {
        alert("Image must be less than 1MB.");
        input.value = "";
      }
    }
  </script>

</body>
</html>
