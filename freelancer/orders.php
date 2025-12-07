<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle filter parameter and whitelist allowed statuses
$statusFilter = $_GET['status'] ?? '';
$allowedStatuses = ['pending', 'active', 'completed', 'cancelled'];

$sql = "
   SELECT o.id, o.status, o.created_at, o.updated_at,
       g.title AS gig_title, g.price,
       u.id AS client_id, u.name AS client_name, u.email AS client_email
   FROM orders o
   JOIN gigs g ON o.gig_id = g.id
   JOIN users u ON o.client_id = u.id
   WHERE g.freelancer_id = ?
";

if (in_array($statusFilter, $allowedStatuses)) {
    $sql .= " AND o.status = ?";
}

$sql .= " ORDER BY o.created_at DESC";

if (in_array($statusFilter, $allowedStatuses)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $statusFilter);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Freelancer Orders</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100 min-h-screen">

<?php include("../components/sidebar.php"); ?>
<?php include("../components/Navbar.php"); ?>

<div class="p-6 sm:ml-64 max-w-7xl mx-auto">
  <h1 class="text-3xl font-bold text-gray-800 mb-6">📦 My Orders</h1>

  <form method="GET" class="mb-8">
    <label for="status" class="mr-4 font-semibold">Filter Orders:</label>
    <select name="status" id="status" onchange="this.form.submit()" class="border border-gray-300 rounded p-2">
      <option value="" <?= $statusFilter === '' ? 'selected' : '' ?>>All</option>
      <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
      <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
      <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
      <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select>
  </form>

  <?php if ($result->num_rows > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 transition p-6 flex flex-col">
          <h2 class="text-xl font-semibold text-gray-900 mb-3 truncate" title="<?= htmlspecialchars($row['gig_title']) ?>">
            <?= htmlspecialchars($row['gig_title']); ?>
          </h2>

          <div class="text-sm text-gray-500 mb-2 space-y-1">
            <p>👤 <span class="font-medium text-gray-700"><?= htmlspecialchars($row['client_name']); ?></span></p>
            <p>📧 <span class="font-medium text-gray-700"><?= htmlspecialchars($row['client_email']); ?></span></p>
            <p>💰 <span class="font-semibold text-gray-800">₹<?= number_format($row['price'], 2); ?></span></p>
          </div>

          <div>
            <span class="inline-block px-3 py-1 text-xs font-medium rounded-full
              <?= $row['status'] === 'completed' ? 'bg-green-100 text-green-700' : '' ?>
              <?= $row['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' ?>
              <?= $row['status'] === 'active' ? 'bg-blue-100 text-blue-700' : '' ?>
              <?= $row['status'] === 'cancelled' ? 'bg-red-100 text-red-700' : '' ?>">
              <?= ucfirst($row['status']); ?>
            </span>
          </div>

          <div class="mt-3 text-xs text-gray-400 space-y-1">
            <p>📅 Ordered: <?= date("M d, Y", strtotime($row['created_at'])); ?></p>
            <?php if ($row['updated_at']): ?>
              <p>🔄 Updated: <?= date("M d, Y", strtotime($row['updated_at'])); ?></p>
            <?php endif; ?>
          </div>

          <div class="mt-auto flex justify-between items-center pt-5 border-t border-gray-200 text-sm font-medium text-gray-600">
            <a href="order_details.php?id=<?= (int)$row['id']; ?>" class="text-blue-600 hover:text-blue-800 transition flex items-center gap-1">
              <i class="fa-solid fa-magnifying-glass"></i> View
            </a>
            <a href="chat.php?user_id=<?= (int)$row['client_id']; ?>" class="text-purple-600 hover:text-purple-800 transition flex items-center gap-1">
              <i class="fa-solid fa-comments"></i> Messages
            </a>
            <?php if ($row['status'] === 'pending'): ?>
              <div class="flex gap-3">
                  <a href="update_order.php?id=<?= (int)$row['id']; ?>&action=accept" class="text-green-600 hover:text-green-800 transition flex items-center gap-1">
                    <i class="fa-solid fa-check"></i> Accept
                  </a>
                  <a href="update_order.php?id=<?= (int)$row['id']; ?>&action=reject" class="text-red-600 hover:text-red-800 transition flex items-center gap-1">
                    <i class="fa-solid fa-xmark"></i> Reject
                  </a>
              </div>
            <?php endif; ?>

          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="bg-white p-10 rounded-2xl shadow text-center text-gray-600">
      🚫 No orders found for your gigs.
    </div>
  <?php endif; ?>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
