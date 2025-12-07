<?php
session_start();
require("../db.php");

// Optional: enforce login if needed
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../index.php");
//     exit();
// }

// $search = trim($_GET['search'] ?? '');
// if ($search === '') {
//     header('Location: /workloop/client/explore.php');
//     exit;
// }

$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';

if ($search === '') {
    header("Location: explore.php");
    exit();
}

// Fetch all categories for dropdown filter (category names as strings)
$categories = [];
$catStmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name ASC");
$catStmt->execute();
$catResult = $catStmt->get_result();
while ($cat = $catResult->fetch_assoc()) {
    $categories[] = $cat;
}
$catStmt->close();


// Base SQL with proper join to get category name
$sql = "SELECT gigs.*, users.name AS freelancer_name, users.profile_image, categories.name AS category_name
        FROM gigs
        JOIN users ON gigs.freelancer_id = users.id
        LEFT JOIN categories ON gigs.category = categories.id";


$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = "gigs.title LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}
if ($category !== '') {
    $where[] = "gigs.category = ?";
    $params[] = (int)$category;
    $types .= 'i'; // integer type
}


if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY gigs.created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Search Results - Workloop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body class="bg-gray-100 text-gray-800 min-h-screen">

    <?php include("../components/Navbar.php"); ?>

    <div class="flex h-full pt-16">
        <div class="w-64 bg-white border-r hidden md:block">
            <?php include("../components/sidebar.php"); ?>
        </div>

        <main class="flex-1 p-6 overflow-y-auto max-w-5xl mx-auto">
            <h1 class="text-3xl font-bold mb-6">Search Results</h1>

            <!-- Search & Category filter form -->
            <form method="GET" class="mb-6 flex flex-wrap gap-4 items-center max-w-lg" action="">
                <input type="text" name="search" placeholder="Search gigs by title..."
                    value="<?= htmlspecialchars($search) ?>"
                    class="flex-grow border border-gray-300 rounded-full px-5 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm" />
                <select name="category"
                    class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>" <?= ($cat['id'] == $category) ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucwords($cat['name'])) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition">Filter</button>
            </form>

            <!-- Display applied filters -->
            <div class="mb-6 flex flex-wrap gap-4 items-center">
                <?php if ($search !== ''): ?>
                    <span class="bg-green-100 text-green-800 rounded px-3 py-1">Title: <?= htmlspecialchars($search) ?></span>
                <?php endif; ?>
                <?php if ($category !== ''): ?>
                    <span class="bg-green-100 text-green-800 rounded px-3 py-1">Category: <?= htmlspecialchars(ucwords($category)) ?></span>
                <?php endif; ?>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <a href="gig_details.php?gig_id=<?= (int)$row['id'] ?>"
                            class="bg-white rounded-lg shadow hover:shadow-lg transition flex flex-col">
                            <img src="../assets/image/uploads/gigs/<?= htmlspecialchars($row['image']) ?>" alt="Gig Image"
                                class="w-full h-48 object-cover rounded-t-lg" />
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold"><?= htmlspecialchars($row['title']) ?></h2>
                                    <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars(substr($row['description'], 0, 80)) ?>...</p>
                                    <p class="text-xs text-gray-400 mt-1 italic">Category: <?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></p>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <img src="../assets/image/user/<?= htmlspecialchars($row['profile_image'] ?? 'default.png') ?>"
                                            alt="Freelancer" class="w-8 h-8 rounded-full object-cover" />
                                        <span class="text-sm"><?= htmlspecialchars($row['freelancer_name']) ?></span>
                                    </div>
                                    <div class="text-green-600 font-bold text-lg">₹<?= number_format($row['price'], 2) ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No gigs found matching your search criteria.</p>
            <?php endif; ?>
        </main>
    </div>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>
