<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require '../db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: categories.php");
    exit();
}

// Fetch category data
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$category) {
    header("Location: categories.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $active = isset($_POST['active']) ? 1 : 0;

    // Handle icon upload (optional)
    $iconPath = $category['icon'];
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/image/cat_icons/'; // Ensure this exists and is writable
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['icon']['name']);
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['icon']['tmp_name'], $targetPath)) {
            $iconPath = 'assets/image/cat_icons/' . $filename;
            // Optionally, delete old icon file here if needed
        } else {
            $message = "Failed to upload new icon.";
        }
    }

    if ($name !== '') {
        $updateStmt = $conn->prepare("UPDATE categories SET name = ?, icon = ?, active = ? WHERE id = ?");
        $updateStmt->bind_param("ssii", $name, $iconPath, $active, $id);
        if ($updateStmt->execute()) {
            $message = "Category updated successfully!";
            // Refresh category data after update
            $category['name'] = $name;
            $category['icon'] = $iconPath;
            $category['active'] = $active;
        } else {
            $message = "Database update failed.";
        }
        $updateStmt->close();
    } else {
        $message = "Name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Category</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-xl mx-auto p-6 mt-20 bg-white rounded shadow-md">
        <h1 class="text-xl font-bold mb-6">Edit Category #<?= $id ?></h1>

        <?php if ($message): ?>
            <div class="mb-4 p-3 rounded <?= strpos($message, 'success') !== false ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block font-semibold mb-1" for="name">Name <span class="text-red-500">*</span></label>
                <input id="name" name="name" type="text" required value="<?= htmlspecialchars($category['name']) ?>"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" />
            </div>

            <div>
                <label class="block font-semibold mb-1">Current Icon</label>
                <?php if (!empty($category['icon'])): ?>
                    <img src="../<?= htmlspecialchars($category['icon']) ?>" alt="Icon" class="w-16 h-16 object-contain" />
                <?php else: ?>
                    <p class="text-gray-500">No icon uploaded.</p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block font-semibold mb-1" for="icon">Upload New Icon (optional)</label>
                <input id="icon" name="icon" type="file" accept="image/*" class="w-full" />
            </div>

            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="active" value="1" <?= $category['active'] ? 'checked' : '' ?> class="form-checkbox" />
                    <span class="ml-2">Active</span>
                </label>
            </div>

            <div class="flex justify-between items-center">
                <a href="categories.php" class="text-gray-600 hover:underline">&larr; Back to Categories</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</body>
</html>
