<?php
session_start();
require("../db.php");

// Check user login and freelancer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$message = '';

// Fetch categories from database to populate dropdown
$categories = [];
$catStmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name ASC");
if (!$catStmt) {
    die("Prepare failed: " . $conn->error);
}
$catStmt->execute();
$catResult = $catStmt->get_result();
if (!$catResult) {
    die("Query failed: " . $conn->error);
}

while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}
$catStmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $delivery_time = intval($_POST['delivery_time']);
    $category = intval($_POST['category']); // category id as integer
    $image = $_FILES['image'];

    // Basic validation
    if ($title && $description && $price > 0 && $delivery_time > 0 && $category && $image['name']) {
        // Validate image size < 1MB
        if ($image['size'] > 1048576) {
            $message = "Image must be less than 1MB.";
        } else {
            $uploadDir = "../assets/image/uploads/gigs/";
            $imgName = time() . "_" . basename($image['name']);
            $targetPath = $uploadDir . $imgName;

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                $freelancer_id = $_SESSION['user_id'];

                $stmt = $conn->prepare("INSERT INTO gigs (freelancer_id, title, description, price, delivery_time, category, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issdiss", $freelancer_id, $title, $description, $price, $delivery_time, $category, $imgName);

                if ($stmt->execute()) {
                    $message = "Gig added successfully!";
                } else {
                    $message = "Error saving gig to database.";
                }
                $stmt->close();
            } else {
                $message = "Failed to upload image.";
            }
        }
    } else {
        $message = "Please fill in all required fields with valid data.";
    }
}
// echo "<pre>";
// print_r($categories);
// echo "</pre>";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Add New Gig - Freelancer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body class="bg-gray-100">
    <?php include("../components/sidebar.php"); ?>
    <?php include("../components/Navbar.php"); ?>

    <main class="ml-80 p-6 max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Add New Gig</h1>

        <?php if ($message): ?>
            <div class="mb-4 text-white p-3 rounded <?= strpos($message, 'success') !== false ? 'bg-green-500' : 'bg-red-500' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-white p-9 rounded shadow-md">
            <div class="mb-4">
                <label class="block mb-1 font-medium">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" required class="w-full border border-gray-300 p-2 rounded" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" />
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4" required class="w-full border border-gray-300 p-2 rounded"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Price (INR) <span class="text-red-500">*</span></label>
                <input type="number" name="price" min="100" required class="w-full border border-gray-300 p-2 rounded" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" />
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Delivery Time (Days) <span class="text-red-500">*</span></label>
                <input type="number" name="delivery_time" min="1" required class="w-full border border-gray-300 p-2 rounded" value="<?= htmlspecialchars($_POST['delivery_time'] ?? '') ?>" />
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Category <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full border border-gray-300 p-2 rounded">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (($_POST['category'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block mb-1 font-medium">Upload Gig Image <span class="text-red-500">*</span></label>
                <input type="file" name="image" accept="image/*" required class="w-full" />
            </div>

            <button type="submit" class="bg-[#1DBF73] text-white px-4 py-2 rounded hover:bg-green-600 transition flex items-center justify-center">
                <i class="fa-solid fa-plus mr-2"></i> Add Gig
            </button>
        </form>
    </main>
</body>

</html>
