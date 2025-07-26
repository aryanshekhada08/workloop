<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<link rel="stylesheet" href="../style/navbar.css">


<nav class="navbar">
  <div class="nav-left">
    <a href="/" class="logo-container">
      <img src="assets/images/logo4.png" alt="WorkLoop Logo" class="logo-img">
    </a>
    <!-- Menu -->
    <ul class="nav-links">
      <li><a href="#">Explore</a></li>
      <li><a href="#">Categories</a></li>
      <li><a href="#">Become a Seller</a></li>
    </ul>
  </div>

  <div class="nav-right">
    <!-- Optional Search (can be controlled via $hideNavbarSearch) -->
    <?php if (empty($hideNavbarSearch)): ?>
      <input type="text" class="search-input" placeholder="Search Workloop...">
    <?php endif; ?>

    <?php if (isset($_SESSION['name']) && isset($_SESSION['role'])): ?>
      <!-- Logged in view -->
      <div class="profile-dropdown">
        <div class="profile-circle"><?= strtoupper(substr($_SESSION['name'], 0, 1)) ?></div>
        <div class="dropdown-content">
          <?php if ($_SESSION['role'] === 'freelancer'): ?>
            <a href="/freelancer/dashboard.php">📊 Dashboard</a>
            <a href="/freelancer/services.php">🛠️ My Services</a>
            <a href="/freelancer/requests.php">📩 My Requests</a>
            <a href="/freelancer/orders.php">💼 My Orders</a>
            <a href="/freelancer/messages.php">💬 Messages</a>
            <a href="/freelancer/notifications.php">🔔 Notifications</a>
            <a href="/freelancer/profile.php">⚙️ Settings</a>
          <?php elseif ($_SESSION['role'] === 'client'): ?>
            <a href="/client/dashboard.php">📊 Dashboard</a>
            <a href="/client/requests.php">📩 My Requests</a>
            <a href="/client/orders.php">💼 My Orders</a>
            <a href="/client/messages.php">💬 Messages</a>
            <a href="/client/notifications.php">🔔 Notifications</a>
            <a href="/client/profile.php">⚙️ Settings</a>
          <?php endif; ?>
          <hr>
          <a href="/logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">Logout</a>
        </div>
      </div>

    <?php else: ?>
      <!-- Not logged in -->
      <button class="login-btn" onclick="openAuthModal('login')">Login</button>
      <button class="join-btn" onclick="openAuthModal('signup')">Join</button>
    <?php endif; ?>
  </div>
</nav>

<script src="/auth-modal.js"></script>
