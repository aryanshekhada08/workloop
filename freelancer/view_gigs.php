<?php
session_start();
require '../db.php';

// Check freelancer authentication
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$freelancer_id = $_SESSION['id'];
$result = $conn->prepare("SELECT * FROM gigs WHERE freelancer_id = ? ORDER BY created_at DESC");
$result->bind_param("i", $freelancer_id);
$result->execute();
$gigs = $result->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Gigs</title>
    <link rel="stylesheet" href="../style/dashboard.css"> <!-- Customize as needed -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Your Gigs</h1>

    <?php if (isset($_SESSION['gig_message'])): ?>
        <div class="mb-4 text-green-600 font-medium">
            <?= $_SESSION['gig_message']; unset($_SESSION['gig_message']); ?>
        </div>
    <?php endif; ?>

    <a href="add_gig.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block">+ Add New Gig</a>

    <?php if ($gigs->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($gig = $gigs->fetch_assoc()): ?>
                <div class="bg-white shadow rounded p-4">
                    <?php if ($gig['image']): ?>
                        <img src="../uploads/gigs/<?= htmlspecialchars($gig['image']) ?>" alt="Gig Image" class="w-full h-40 object-cover rounded mb-2">
                    <?php endif; ?>
                    <h2 class="text-xl font-semibold"><?= htmlspecialchars($gig['title']) ?></h2>
                    <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars(substr($gig['description'], 0, 100)) ?>...</p>
                    <p class="mt-2 font-bold text-green-700">₹<?= htmlspecialchars($gig['price']) ?></p>

                    <div class="mt-4 flex justify-between">
                        <a href="edit_gig.php?id=<?= $gig['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="delete_gig.php?id=<?= $gig['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this gig?')">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>You haven't posted any gigs yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
