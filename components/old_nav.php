<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Prevent undefined variable warning
if (!isset($hideNavbarSearch)) $hideNavbarSearch = false;
?>

<link rel="stylesheet" href="/style/navbar.css">

<nav class="bg-white shadow-md py-3 px-4">
  <div class="max-w-7xl mx-auto flex items-center justify-between">

    <!-- Logo -->
    <a href="/index.php" class="flex items-center space-x-2">
      <img src="/assets/logo.png" alt="WorkLoop Logo" class="h-8">
    </a>

    <!-- Search (Only if not hidden) -->
    <?php if (empty($hideNavbarSearch)): ?>
      <div class="hidden md:block w-full max-w-md mx-4">
        <input
          type="text"
          placeholder="Search Workloop..."
          class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
        >
      </div>
    <?php endif; ?>

    <!-- Right Side (User or Guest) -->
    <div class="flex items-center space-x-4">
      <?php if (isset($_SESSION['user_id']) && isset($_SESSION['name']) && isset($_SESSION['role'])): ?>
        <!-- Logged In Dropdown -->
        <div class="relative group">
          <div class="w-10 h-10 bg-green-500 text-white flex items-center justify-center rounded-full cursor-pointer uppercase">
            <?= strtoupper(substr($_SESSION['name'], 0, 1)) ?>
          </div>
          <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-10 text-sm">

            <?php if ($_SESSION['role'] === 'freelancer'): ?>
              <a href="/freelancer/dashboard.php" class="block px-4 py-2 hover:bg-gray-100">📊 Dashboard</a>
              <a href="/freelancer/services.php" class="block px-4 py-2 hover:bg-gray-100">🛠️ My Services</a>
              <a href="/freelancer/requests.php" class="block px-4 py-2 hover:bg-gray-100">📩 My Requests</a>
              <a href="/freelancer/orders.php" class="block px-4 py-2 hover:bg-gray-100">💼 My Orders</a>
              <a href="/freelancer/messages.php" class="block px-4 py-2 hover:bg-gray-100">💬 Messages</a>
              <a href="/freelancer/notifications.php" class="block px-4 py-2 hover:bg-gray-100">🔔 Notifications</a>
              <a href="/freelancer/profile.php" class="block px-4 py-2 hover:bg-gray-100">⚙️ Settings</a>
            <?php elseif ($_SESSION['role'] === 'client'): ?>
              <a href="/client/dashboard.php" class="block px-4 py-2 hover:bg-gray-100">📊 Dashboard</a>
              <a href="/client/requests.php" class="block px-4 py-2 hover:bg-gray-100">📩 My Requests</a>
              <a href="/client/orders.php" class="block px-4 py-2 hover:bg-gray-100">💼 My Orders</a>
              <a href="/client/messages.php" class="block px-4 py-2 hover:bg-gray-100">💬 Messages</a>
              <a href="/client/notifications.php" class="block px-4 py-2 hover:bg-gray-100">🔔 Notifications</a>
              <a href="/client/profile.php" class="block px-4 py-2 hover:bg-gray-100">⚙️ Settings</a>
            <?php endif; ?>

            <hr>
            <a href="/logout.php" class="block px-4 py-2 text-red-500 hover:bg-red-100 font-medium">🚪 Logout</a>
          </div>
        </div>

      <?php else: ?>
        <!-- Guest View -->
        <button onclick="openAuthModal('login')" class="text-gray-700 hover:text-green-600 font-medium">Login</button>
        <button onclick="openAuthModal('signup')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full font-medium">Join</button>
      <?php endif; ?>
    </div>

  </div>
</nav>
