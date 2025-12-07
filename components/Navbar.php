<?php

require_once(__DIR__ . '/../db.php');

// User session data
$role = $_SESSION['role'] ?? '';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>WorkLoop Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- PNG favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
<link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

</head>
<body>
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

            <!-- Help & Right Menu -->
            <a href="/workloop/help_contact.php" class="text-gray-700 hover:text-white hover:bg-green-600 px-3 py-1 rounded transition font-semibold">
                Help & Contact Admin
            </a>
            <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                <a href="/workloop/<?= $role ?>/messages.php" class="relative hover:text-green-600 transition" aria-label="Messages">
                    🔔<span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">2</span>
                </a>
                <a href="/workloop/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition">Logout</a>
            </div>
        <?php else: ?>
            <!-- Not logged in -->
            <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                <button onclick="openAuthModal('login')" class="hover:text-green-600">Login</button>
                <button onclick="openAuthModal('signup')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full">Join</button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Mobile Search Bar -->
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

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden hidden flex-col space-y-3 text-sm font-medium px-6 pb-4 border-t border-gray-200">
        <?php if ($isLoggedIn): ?>
            <a href="/workloop/<?= $role ?>/messages.php" class="relative hover:text-green-600 transition text-center" aria-label="Messages">
                🔔<span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">2</span>
            </a>
            <a href="/workloop/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition text-center">Logout</a>
        <?php else: ?>
            <a href="/workloop/about.php" class="hover:text-green-600 ">About us</a>
            
            <button onclick="openAuthModal('login')" class="hover:text-green-600">Login</button>
            <button onclick="openAuthModal('signup')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full w-full mt-2">Join</button>
        <?php endif; ?>
    </div>
</nav>

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
</body>
</html>
