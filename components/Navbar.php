<?php
$role = $_SESSION['role'] ?? '';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<nav class="bg-white border-b shadow px-6 py-4 sticky top-0 z-50">
  <div class="flex justify-between items-center">
    <!-- Logo -->
    <a href="/index.php" class="flex items-center space-x-1">
      <img src="../assets/logo.png" alt="WorkLoop Logo" class="h-9 w-auto object-contain">
    </a>

    <!-- Mobile Search Icon -->
    <?php if (empty($hideNavbarSearch)): ?>
      <button id="mobileSearchToggle" class="md:hidden mr-3 text-xl">
        🔍
      </button>
    <?php endif; ?>

    <!-- Hamburger Button -->
    <button id="mobileMenuBtn" class="md:hidden text-2xl focus:outline-none">
      ☰
    </button>

    <!-- Desktop Search -->
    <div class="hidden md:flex flex-1 justify-center px-6 transition-all duration-300">
      <?php if (empty($hideNavbarSearch)): ?>
        <input
          type="text"
          placeholder="Search Workloop..."
          class="w-full max-w-lg border border-gray-300 rounded-full px-5 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm"
        >
      <?php endif; ?>
    </div>

    <!-- Right Menu (Desktop) -->
    <div class="hidden md:flex items-center space-x-4 text-sm font-medium transition-all duration-300">
      <?php if ($isLoggedIn): ?>
        <?php if ($role === 'client'): ?>
          <!-- <a href="/client/dashboard.php" class="hover:text-green-600 transition">📊 Dashboard</a>
          <a href="/client/requests.php" class="hover:text-green-600 transition">📩 Requests</a>
          <a href="/client/orders.php" class="hover:text-green-600 transition">💼 Orders</a>
          <a href="/client/messages.php" class="hover:text-green-600 transition">💬</a>
          <a href="/client/notifications.php" class="relative hover:text-green-600 transition">
            🔔<span class="absolute top-0 right-0 bg-red-500 text-white text-xs px-1 rounded-full">2</span>
          </a>
          <a href="/client/profile.php" class="hover:text-green-600 transition">⚙️</a> -->
        <?php elseif ($role === 'freelancer'): ?>
          <!-- <a href="/freelancer/dashboard.php" class="hover:text-green-600 transition">📊 Dashboard</a>
          <a href="/freelancer/services.php" class="hover:text-green-600 transition">🛠️ Services</a>
          <a href="/freelancer/requests.php" class="hover:text-green-600 transition">📩 Requests</a>
          <a href="/freelancer/orders.php" class="hover:text-green-600 transition">💼 Orders</a>
          <a href="/freelancer/messages.php" class="hover:text-green-600 transition">💬</a>
          <a href="/freelancer/notifications.php" class="relative hover:text-green-600 transition"> -->
            <!-- 🔔<span class="absolute top-0 right-0 bg-red-500 text-white text-xs px-1 rounded-full">2</span>
          </a>
          <a href="/freelancer/profile.php" class="hover:text-green-600 transition">⚙️</a> -->
        <?php endif; ?>
        <a href="/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition">Logout</a>
      <?php else: ?>
        <button onclick="openAuthModal('login')" class="hover:text-green-600">Login</button>
        <button onclick="openAuthModal('signup')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full">Join</button>
      <?php endif; ?>
    </div>
  </div>

  <!-- Mobile Search Dropdown -->
  <?php if (empty($hideNavbarSearch)): ?>
    <div id="mobileSearch" class="md:hidden hidden mt-3">
      <input
        type="text"
        placeholder="Search Workloop..."
        class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
      >
    </div>
  <?php endif; ?>

  <!-- Mobile Menu Dropdown -->
  <div id="mobileMenu" class="md:hidden hidden flex-col mt-4 space-y-3 text-sm font-medium transition-all duration-300">
    <?php if ($isLoggedIn): ?>
      <?php if ($role === 'client'): ?>
        <a href="/client/dashboard.php" class="hover:text-green-600 transition">📊 Dashboard</a>
        <a href="/client/requests.php" class="hover:text-green-600 transition">📩 Requests</a>
        <a href="/client/orders.php" class="hover:text-green-600 transition">💼 Orders</a>
        <a href="/client/messages.php" class="hover:text-green-600 transition">💬 Messages</a>
        <a href="/client/notifications.php" class="hover:text-green-600 transition">🔔 Notifications</a>
        <a href="/client/profile.php" class="hover:text-green-600 transition">⚙️ Profile</a>
      <?php elseif ($role === 'freelancer'): ?>
        <a href="/freelancer/dashboard.php" class="hover:text-green-600 transition">📊 Dashboard</a>
        <a href="/freelancer/services.php" class="hover:text-green-600 transition">🛠️ Services</a>
        <a href="/freelancer/requests.php" class="hover:text-green-600 transition">📩 Requests</a>
        <a href="/freelancer/orders.php" class="hover:text-green-600 transition">💼 Orders</a>
        <a href="/freelancer/messages.php" class="hover:text-green-600 transition">💬 Messages</a>
        <a href="/freelancer/notifications.php" class="hover:text-green-600 transition">🔔 Notifications</a>
        <a href="/freelancer/profile.php" class="hover:text-green-600 transition">⚙️ Profile</a>
      <?php endif; ?>
      <!-- <a href="/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition text-center">Logout</a> -->
    <?php else: ?>
      <button onclick="openAuthModal('login')" class="hover:text-green-600">Login</button>
      <button onclick="openAuthModal('signup')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full">Join</button>
    <?php endif; ?>
  </div>
</nav>

<!-- JS Toggle -->
<script>
  const mobileMenuBtn = document.getElementById("mobileMenuBtn");
  const mobileMenu = document.getElementById("mobileMenu");
  const mobileSearchToggle = document.getElementById("mobileSearchToggle");
  const mobileSearch = document.getElementById("mobileSearch");

  mobileMenuBtn?.addEventListener("click", () => {
    mobileMenu.classList.toggle("hidden");
    mobileMenu.classList.toggle("flex");
  });

  mobileSearchToggle?.addEventListener("click", () => {
    mobileSearch.classList.toggle("hidden");
  });
</script>
