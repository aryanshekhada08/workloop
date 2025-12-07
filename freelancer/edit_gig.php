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

// Fetch categories for dropdown
$categories = [];
$catStmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name ASC");
$catStmt->execute();
$catResult = $catStmt->get_result();
while ($catRow = $catResult->fetch_assoc()) {
    $categories[] = $catRow;
}
$catStmt->close();

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
    $category = intval($_POST['category']);
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
        $stmtUpdate = $conn->prepare("UPDATE gigs SET title=?, description=?, price=?, delivery_time=?, category=?, image=? WHERE id=? AND freelancer_id=?");
        $stmtUpdate->bind_param("ssdiissi", $title, $description, $price, $delivery_time, $category, $imgName, $gig_id, $user_id);
        if ($stmtUpdate->execute()) {
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
        $stmtUpdate->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Gig</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100">

<?php include("../components/sidebar.php"); ?>
<?php include("../components/Navbar.php"); ?>

<div class="ml-80 p-6 max-w-4xl mx-auto">
  <h1 class="text-3xl font-bold mb-6">Edit Gig</h1>

  <?php if ($message): ?>
    <div class="mb-6 p-4 rounded <?= strpos($message, 'success') !== false ? 'bg-green-600 text-white' : 'bg-red-600 text-white' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="bg-white p-9 rounded shadow-md space-y-6">
    <div>
      <label class="block mb-1 font-medium" for="title">Title <span class="text-red-500">*</span></label>
      <input id="title" type="text" name="title" value="<?= htmlspecialchars($gig['title']) ?>" required placeholder="Enter gig title" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"/>
    </div>

    <div>
      <label class="block mb-1 font-medium" for="description">Description <span class="text-red-500">*</span></label>
      <textarea id="description" name="description" rows="4" required placeholder="Describe your gig" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($gig['description']) ?></textarea>
    </div>

    <div>
      <label class="block mb-1 font-medium" for="price">Price (INR) <span class="text-red-500">*</span></label>
      <input id="price" type="number" step="0.01" name="price" value="<?= htmlspecialchars($gig['price']) ?>" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"/>
    </div>

    <div>
      <label class="block mb-1 font-medium" for="delivery_time">Delivery Time (Days) <span class="text-red-500">*</span></label>
      <input id="delivery_time" type="number" name="delivery_time" value="<?= htmlspecialchars($gig['delivery_time']) ?>" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"/>
    </div>

    <div>
      <label class="block mb-1 font-medium" for="category">Category <span class="text-red-500">*</span></label>
      <select id="category" name="category" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Select Category</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= ($gig['category'] == $cat['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="block mb-1 font-medium" for="image">Upload New Image (optional)</label>
      <input id="image" type="file" name="image" accept="image/*" onchange="validateFileSize(this)" class="w-full border border-gray-300 p-2 rounded" />
    </div>

    <div class="flex items-center gap-4 mb-6">
      <span class="text-sm text-gray-600">Current Image:</span>
      <img src="../assets/image/uploads/gigs/<?= htmlspecialchars($gig['image']) ?>" alt="Gig Image" class="h-20 rounded border" />
    </div>

    <button type="submit" class="bg-[#1DBF73] text-white px-4 py-2 rounded hover:bg-green-600 transition focus:outline-none focus:ring-2 focus:ring-green-500">
      <i class="fa-solid fa-save mr-2"></i> Update Gig
    </button>
  </form>
</div>

<script>
  function validateFileSize(input) {
    const file = input.files[0];
    if (file && file.size > 1048576) {
      alert("Image must be less than 1MB.");
      input.value = ""; // Reset file input
    }
  }
</script>

</body>
</html>
