<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../index.php");
    exit();
}

$freelancer_id = $_SESSION['user_id'];

// Fetch freelancer's orders
$query = "SELECT o.id, o.title, o.description, o.status, o.delivery_file, 
                 u.name AS client_name, o.created_at 
          FROM orders o 
          JOIN users u ON o.client_id = u.id 
          WHERE o.freelancer_id = ?
          ORDER BY o.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders - Freelancer</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-6xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">My Orders</h1>

    <div class="bg-white shadow rounded-lg p-6">
      <?php if ($result->num_rows > 0): ?>
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-200">
              <th class="p-3 text-left">Title</th>
              <th class="p-3">Client</th>
              <th class="p-3">Status</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="border-b">
              <td class="p-3"><?php echo htmlspecialchars($row['title']); ?></td>
              <td class="p-3"><?php echo htmlspecialchars($row['client_name']); ?></td>
              <td class="p-3">
                <span class="px-2 py-1 rounded text-sm 
                  <?php 
                    echo $row['status'] === 'pending' ? 'bg-yellow-200 text-yellow-800' : 
                         ($row['status'] === 'active' ? 'bg-blue-200 text-blue-800' : 
                         ($row['status'] === 'delivered' ? 'bg-green-200 text-green-800' : 
                         'bg-red-200 text-red-800'));
                  ?>">
                  <?php echo ucfirst($row['status']); ?>
                </span>
              </td>
              <td class="p-3 space-x-2">
                <?php if ($row['status'] === 'pending'): ?>
                  <a href="update_order.php?id=<?php echo $row['id']; ?>&action=accept" 
                     class="bg-blue-500 text-white px-3 py-1 rounded">Accept</a>
                  <a href="update_order.php?id=<?php echo $row['id']; ?>&action=cancel" 
                     class="bg-red-500 text-white px-3 py-1 rounded">Cancel</a>
                <?php elseif ($row['status'] === 'active'): ?>
                  <a href="deliver_order.php?id=<?php echo $row['id']; ?>" 
                     class="bg-green-500 text-white px-3 py-1 rounded">Deliver</a>
                <?php elseif ($row['status'] === 'delivered'): ?>
                  <span class="text-gray-500">Waiting client approval</span>
                <?php else: ?>
                  <span class="text-gray-500">Completed/Cancelled</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No orders found.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
