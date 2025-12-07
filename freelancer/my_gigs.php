<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id, title, price, delivery_time, image FROM gigs WHERE freelancer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Gigs</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100">

<?php include("../components/sidebar.php"); ?>
<?php include("../components/Navbar.php"); ?>

<div class="ml-64 p-6 max-w-7xl mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">My Gigs</h1>
    <a href="add_gig.php" class="bg-[#1DBF73] text-white px-4 py-2 rounded hover:bg-green-600 transition">
      <i class="fa-solid fa-plus mr-2"></i> Add New Gig
    </a>
  </div>

  <?php if (isset($_SESSION['gig_message'])): ?>
    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
      <?= htmlspecialchars($_SESSION['gig_message']); unset($_SESSION['gig_message']); ?>
    </div>
  <?php endif; ?>

  <?php if ($result->num_rows > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <?php while ($gig = $result->fetch_assoc()): ?>
        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-4 flex flex-col">
          <img src="../assets/image/uploads/gigs/<?= htmlspecialchars($gig['image']) ?>" alt="Gig Image" class="w-full h-40 object-cover rounded-md mb-4">
          <h2 class="text-xl font-semibold mb-2 truncate"><?= htmlspecialchars($gig['title']) ?></h2>
          <p class="text-gray-600 mb-4">₹<?= number_format($gig['price'], 2) ?> • <?= (int)$gig['delivery_time'] ?> days</p>
          <div class="mt-auto flex justify-between">
            <a href="edit_gig.php?id=<?= (int)$gig['id'] ?>" class="text-blue-600 hover:underline flex items-center">
              <i class="fa-solid fa-pen mr-1"></i> Edit
            </a>
            <a href="delete_gig.php?id=<?= (int)$gig['id'] ?>" class="text-red-600 hover:underline flex items-center"
               onclick="return confirm('Are you sure you want to delete this gig?');">
              <i class="fa-solid fa-trash mr-1"></i> Delete
            </a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="text-gray-500 text-center py-20">You haven't added any gigs yet.</p>
  <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
?>

</body>
</html>
