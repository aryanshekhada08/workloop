<!-- admin/includes/sidebar.php -->

<!-- Mobile toggle for sidebar -->
<div class="md:hidden flex items-center justify-between bg-white p-4 shadow sticky top-0 z-50">
  <h2 class="text-xl font-bold text-green-600">Admin Panel</h2>
  <button onclick="toggleSidebar()" aria-label="Toggle Sidebar"
    class="text-3xl text-gray-800 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-green-600 rounded">
    &#9776;
  </button>
</div>

<!-- Sidebar container -->
<aside id="adminSidebar"
  class="bg-white md:block hidden w-64 p-6 shadow-xl md:min-h-screen fixed md:relative top-0 left-0 z-50 border-r border-gray-200">

  <h2 class="text-3xl font-extrabold text-green-600 mb-10 hidden md:block select-none">
    Admin Panel
  </h2>

  <nav class="flex flex-col space-y-5 text-gray-700 font-medium text-lg">

    <a href="../admin/dashboard.php"
      class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 hover:text-green-700 transition">
      <span>📊</span> Dashboard
    </a>

    <a href="users.php"
      class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 hover:text-green-700 transition">
      <span>👤</span> Manage Users
    </a>
    
    <a href="categories.php"
      class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 hover:text-green-700 transition">
      <span>🗂</span> Manage Categories
    </a>

    <a href="admin_reports.php"
      class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 hover:text-green-700 transition">
      <span>📩</span> Reports
    </a>

  
    <a href="admin_withdrawals.php"
      class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 hover:text-green-700 transition">
      <span>💰</span> Payments
    </a>

    
    <a href="admin_platform_fees.php"
      class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 hover:text-green-700 transition">
      <span>📊</span> Platform Fees
    </a>

    <a href="settings.php"
      class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-100 hover:text-green-700 transition">
      <span>⚙️</span> Settings
    </a>

    <a href="logout.php"
      class="mt-auto block px-4 py-2 rounded-lg text-red-600 hover:text-red-800 hover:underline font-semibold cursor-pointer">
      <span>🚪</span> Logout
    </a>

  </nav>
</aside>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    sidebar.classList.toggle('hidden');
  }
</script>
