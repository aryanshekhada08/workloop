<?php

require_once(__DIR__ . '/../db.php');

// User role and login status
$role = $_SESSION['role'] ?? '';
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch categories for search dropdown (optional; not used here but available)
$categories = [];
$catResult = $conn->query("SELECT DISTINCT category FROM gigs ORDER BY category ASC");
while ($cat = $catResult->fetch_assoc()) {
    $categories[] = $cat['category'];
}
?>

<nav class="bg-white border-b shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        <!-- Logo -->
        <a href="/workloop/index.php" class="flex items-center space-x-2">
            <img src="/workloop/assets/logo.png" alt="WorkLoop Logo" class="h-9 w-auto object-contain" />
        </a>

        <?php if ($isLoggedIn): ?>
            <!-- Desktop Search Bar -->
            <div class="flex-1 mx-6 hidden md:flex justify-center">
                <form method="GET" action="/workloop/client/search.php" class="flex w-full max-w-lg" id="navbarSearchForm">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search gigs by title..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        class="flex-grow border border-gray-300 rounded-full px-5 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm"
                        autocomplete="off"
                    />
                    <button type="submit" class="ml-2 bg-green-600 text-white rounded-full px-4 flex items-center justify-center hover:bg-green-700" aria-label="Search gigs">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Right Menu -->
          <a href="help_contact.php" 
          class="text-gray-700 hover:text-white hover:bg-green-600 px-3 py-1 rounded transition font-semibold">
           Help & Contact Admin
           </a>
            <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                <?php if ($role === 'client'): ?>
                    <a href="/client/notifications.php" class="relative hover:text-green-600 transition" aria-label="Notifications">
                        🔔<span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">2</span>
                    </a>
                <?php elseif ($role === 'freelancer'): ?>
                    <a href="/freelancer/notifications.php" class="relative hover:text-green-600 transition" aria-label="Notifications">
                        🔔<span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">2</span>
                    </a>
                  
                <?php endif; ?>
                <a href="/workloop/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition">Logout</a>
            </div>
        <?php else: ?>
            <!-- When not logged in -->
            <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                <button onclick="openAuthModal('login')" class="hover:text-green-600">Login</button>
                <button onclick="openAuthModal('signup')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full">Join</button>
            </div>
        <?php endif; ?>

        <!-- Mobile icons -->
        <div class="flex md:hidden items-center space-x-4">
            <button id="mobileMenuBtn" class="text-2xl focus:outline-none" aria-label="Toggle menu">☰</button>
        </div>
    </div>

    <!-- Mobile Search Dropdown -->
    <div id="mobileSearch" class="md:hidden hidden px-6 pb-3 border-b border-gray-200">
        <form method="GET" action="/workloop/client/search.php" class="flex space-x-2" id="mobileSearchForm">
            <input
                type="text"
                name="search"
                placeholder="Search gigs by title..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                class="flex-grow border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                autocomplete="off"
            />
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Search</button>
        </form>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div id="mobileMenu" class="md:hidden hidden flex-col space-y-3 text-sm font-medium px-6 pb-4 border-t border-gray-200">
        <?php if ($isLoggedIn): ?>
            <?php if ($role === 'client'): ?>
                <a href="/client/dashboard.php" class="hover:text-green-600 transition">Dashboard</a>
                <a href="/client/requests.php" class="hover:text-green-600 transition">Requests</a>
                <a href="/client/orders.php" class="hover:text-green-600 transition">Orders</a>
                <a href="/client/messages.php" class="hover:text-green-600 transition">Messages</a>
                <a href="/client/notifications.php" class="hover:text-green-600 transition">Notifications</a>
                <a href="/client/profile.php" class="hover:text-green-600 transition">Profile</a>
            <?php elseif ($role === 'freelancer'): ?>
                <a href="/freelancer/dashboard.php" class="hover:text-green-600 transition">Dashboard</a>
                <a href="/freelancer/services.php" class="hover:text-green-600 transition">Services</a>
                <a href="/freelancer/requests.php" class="hover:text-green-600 transition">Requests</a>
                <a href="/freelancer/orders.php" class="hover:text-green-600 transition">Orders</a>
                <a href="/freelancer/messages.php" class="hover:text-green-600 transition">Messages</a>
                <a href="/freelancer/notifications.php" class="hover:text-green-600 transition">Notifications</a>
                <a href="/freelancer/profile.php" class="hover:text-green-600 transition">Profile</a>
            <?php endif; ?>
            <a href="/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition text-center">Logout</a>
        <?php else: ?>
            <button onclick="openAuthModal('login')" class="hover:text-green-600">Login</button>
            <button onclick="openAuthModal('signup')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full w-full mt-2">Join</button>
        <?php endif; ?>
    </div>
</nav>

<script>
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const mobileMenu = document.getElementById("mobileMenu");
    const mobileSearchToggle = document.getElementById("mobileSearchToggle");
    const mobileSearch = document.getElementById("mobileSearch");

    mobileMenuBtn?.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden");
    });

    // Uncomment and use if you add a mobile search toggle button in future
    // mobileSearchToggle?.addEventListener("click", () => {
    //     mobileSearch.classList.toggle("hidden");
    // });
</script>

<script>
    // Prevent submitting empty search on desktop and mobile forms
    function setupSearchEmptyValidation(formId, redirectUrl) {
        const form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function (e) {
            const input = form.querySelector('input[name="search"]');
            if (input && input.value.trim() === '') {
                e.preventDefault();
                window.location.href = redirectUrl;
            }
        });
    }
    document.addEventListener('DOMContentLoaded', () => {
        setupSearchEmptyValidation('navbarSearchForm', '/workloop/client/explore.php');
        setupSearchEmptyValidation('mobileSearchForm', '/workloop/client/explore.php');
    });
</script>
