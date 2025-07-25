<!-- admin/includes/sidebar.php -->

<!-- Mobile toggle -->
<div class="md:hidden flex items-center justify-between bg-white p-4 shadow">
  <h2 class="text-xl font-bold text-green-600">Admin Panel</h2>
  <button onclick="toggleSidebar()" class="text-2xl text-gray-800">&#9776;</button>
</div>

<!-- Sidebar for Desktop + Collapsible Mobile -->
<aside id="adminSidebar" class="bg-white md:block hidden w-64 p-6 shadow-xl md:min-h-screen fixed md:relative top-0 left-0 z-50">
  <h2 class="text-2xl font-bold text-green-600 mb-8 hidden md:block">Admin Panel</h2>
  <nav class="space-y-4 text-gray-800">
    <a href="../admin/dashboard.php" class="block hover:text-green-600">📊 Dashboard</a>
    <a href="users.php" class="block hover:text-green-600">👤 Manage Users</a>
    <a href="categories.php" class="block hover:text-green-600">🗂 Manage Categories</a>
    <a href="/services.php" class="block hover:text-green-600">🛠 Services</a>
    <a href="/admin/requests.php" class="block hover:text-green-600">📩 Requests</a>
    <a href="/admin/orders.php" class="block hover:text-green-600">📦 Orders</a>
    <a href="logout.php" class="block text-red-500 hover:underline">🚪 Logout</a>
  </nav>
</aside>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    sidebar.classList.toggle('hidden');
  }
</script>

