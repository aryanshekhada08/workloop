<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  
</head>
<body>
  <!-- <?php if (session_status() == PHP_SESSION_NONE) session_start(); ?> -->
<link rel="stylesheet" href="css/navbar.css">

<nav class="navbar">
    <div class="nav-left">
  <a href="/" class="logo-container">
    <img src="assets/logo4.png" alt="WorkLoop Logo" class="logo-img">
  </a>
    <!-- Menu -->
    <ul class="nav-links">  
      <li><a href="#">Explore</a></li>
      <li><a href="#">Categories</a></li>
      <li><a href="#">Become a Seller</a></li>
    </ul>
  </div>

  <div class="nav-right">
    <!-- Optional Search (hidden on landing if $hideNavbarSearch is set) -->
    <?php if (empty($hideNavbarSearch)): ?>
      <input type="text" class="search-input" placeholder="Search Workloop...">
    <?php endif; ?>

    <?php if (isset($_SESSION['name'])): ?>
      <!-- Profile -->
      <div class="profile-dropdown">
        <div class="profile-circle"><?= strtoupper(substr($_SESSION['name'], 0, 1)) ?></div>
        <div class="dropdown-content">
          <a href="/user/dashboard.php">📊 Dashboard</a>
          <a href="/user/services.php">🛠️ My Services</a>
          <a href="/user/requests.php">📩 My Requests</a>
          <a href="/user/orders.php">💼 My Orders</a>
          <a href="/user/messages.php">💬 Messages</a>
          <a href="/user/notifications.php">🔔 Notifications</a>
          <a href="/user/profile.php">⚙️ Settings</a>
          <hr>
          <a href="logout.php">🚪 Logout</a>
        </div>
      </div>
    <?php else: ?>
      <!-- Login/Join -->
   <button class="login-btn" onclick="openAuthModal('login')">Login</button>
<button class="join-btn" onclick="openAuthModal('signup')">Join</button>
    <?php endif; ?>
  </div>
</nav>
<!-- <script>
  function toggleMenu() {
    const nav = document.getElementById("navLinks");
    nav.classList.toggle("active");
  }
</script> -->
<script src="../auth-modal.js"></script>
</html>