<?php
session_start();
require("../db.php");



// Redirect if not client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

// Fetch gigs and associated freelancer info
$sql = "SELECT gigs.*, users.name AS freelancer_name, users.profile_image 
        FROM gigs 
        JOIN users ON gigs.freelancer_id = users.id 
        ORDER BY gigs.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Explore Gigs - Workloop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>
<body class="bg-gray-100 text-gray-800 h-screen overflow-hidden">

    <!-- Navbar -->
    <?php include("../components/Navbar.php"); ?>

    <div class="flex h-full pt-16"> <!-- Space for fixed navbar -->

        <!-- Sidebar -->
        <div class="w-64 bg-white border-r hidden md:block">
            <?php include("../components/sidebar.php"); ?>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-y-auto">
            <h1 class="text-3xl font-bold mb-6">Explore Freelance Gigs</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <a href="gig_details.php?gig_id=<?= (int)$row['id'] ?>" class="bg-white rounded-lg shadow hover:shadow-lg transition duration-300 flex flex-col">
                        <img src="../assets/image/uploads/gigs/<?= htmlspecialchars($row['image']) ?>" alt="Gig Image" class="w-full h-48 object-cover rounded-t-lg">
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <h2 class="text-lg font-semibold"><?= htmlspecialchars($row['title']) ?></h2>
                                <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars(substr($row['description'], 0, 80)) ?>...</p>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <img src="../assets/image/user/<?= htmlspecialchars($row['profile_image'] ?? 'default.png') ?>" alt="Freelancer" class="w-8 h-8 rounded-full object-cover">
                                    <span class="text-sm"><?= htmlspecialchars($row['freelancer_name']) ?></span>
                                </div>
                                <div class="text-green-600 font-bold text-lg">₹<?= number_format($row['price'], 2) ?></div>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </main>
    </div>

</body>
</html>
