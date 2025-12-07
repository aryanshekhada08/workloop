<?php
require("../db.php");
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid gig ID.";
    exit;
}

$gig_id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM gigs WHERE id = $gig_id");

if (mysqli_num_rows($query) == 0) {
    echo "Gig not found.";
    exit;
}

$gig = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Gig Details - <?= htmlspecialchars($gig['title']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <img src="../uploads/gigs/<?= htmlspecialchars($gig['image']) ?>" alt="Gig Image" class="w-full h-64 object-cover rounded mb-6">
    
    <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($gig['title']) ?></h1>
    <p class="text-sm text-gray-500 mb-2">Category: <span class="font-medium"><?= htmlspecialchars($gig['category']) ?></span></p>
    <p class="text-green-600 text-xl font-bold mb-4">Price: ₹<?= htmlspecialchars($gig['price']) ?></p>

    <h2 class="text-xl font-semibold mb-2">Description</h2>
    <p class="text-gray-700 mb-6"><?= nl2br(htmlspecialchars($gig['description'])) ?></p>

    <a href="view_gigs.php" class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">← Back to My Gigs</a>
  </div>

</body>
</html>
