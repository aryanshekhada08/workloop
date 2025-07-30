<!-- components/sidebar.php -->
<?php
$role = $_SESSION['role'] ?? '';
?>

<div class="w-64 h-screen bg-white shadow-lg fixed top-0 left-0 z-40 flex flex-col justify-between">
  <div>
    <!-- Sidebar Links -->
    <nav class="px-4 py-6">
      <ul class="space-y-2 text-gray-700 font-medium">

        <li>
          <a href="dashboard.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
            <i class="fa-solid fa-house mr-2"></i> Dashboard
          </a>
        </li>

        <?php if ($role === 'freelancer'): ?>
          <li>
            <a href="my_gigs.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
              <i class="fa-solid fa-briefcase mr-2"></i> My Gigs
            </a>
          </li>
          <li>
            <a href="add_gig.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
              <i class="fa-solid fa-circle-plus mr-2"></i> Add New Gig
            </a>
          </li>
          <li>
            <a href="orders.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
              <i class="fa-solid fa-box mr-2"></i> Orders
            </a>
          </li>
          <li>
            <a href="withdraw.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
              <i class="fa-solid fa-money-bill-wave mr-2"></i> Withdraw
            </a>
          </li>
        <?php elseif ($role === 'client'): ?>
          <li>
            <a href="browse.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
              <i class="fa-solid fa-search mr-2"></i> Browse Services
            </a>
          </li>
          <li>
            <a href="my-requests.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
              <i class="fa-solid fa-receipt mr-2"></i> My Requests
            </a>
          </li>
        <?php endif; ?>

        <li>
          <a href="messages.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
            <i class="fa-solid fa-envelope mr-2"></i> Messages
          </a>
        </li>
        <li>
          <a href="profile.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
            <i class="fa-solid fa-user mr-2"></i> Profile
          </a>
        </li>
        <li>
          <a href="settings.php" class="flex items-center p-3 rounded-lg hover:bg-[#1DBF73] hover:text-white transition">
            <i class="fa-solid fa-gear mr-2"></i> Settings
          </a>
        </li>
        <li>
          <a href="../logout.php" class="flex items-center p-3 rounded-lg hover:bg-red-500 hover:text-white transition">
            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
          </a>
        </li>

      </ul>
    </nav>
  </div>
</div>
