<?php
session_start();
require("../db.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = true;
    $errors = [];

    if (isset($_POST['site_name'])) {
        $settings = [
            'site_name' => trim($_POST['site_name']),
            'site_description' => trim($_POST['site_description']),
            'site_email' => trim($_POST['site_email']),
            'site_phone' => trim($_POST['site_phone']),
            'platform_fee_percentage' => (float)$_POST['platform_fee_percentage'],
            'min_withdrawal_amount' => (float)$_POST['min_withdrawal_amount'],
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
            'allow_registration' => isset($_POST['allow_registration']) ? 1 : 0,
        ];

        foreach ($settings as $key => $value) {
            $stmt = $conn->prepare("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->bind_param("sss", $key, $value, $value);
            if (!$stmt->execute()) {
                $success = false;
                $errors[] = "Failed to update $key";
            }
            $stmt->close();
        }
    }

    if ($success && empty($errors)) {
        $_SESSION['success'] = "Settings updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update some settings: " . implode(", ", $errors);
    }
    
    header("Location: settings.php");
    exit();
}

// Fetch current settings with dummy fallback
function getSetting($conn, $key, $default = '') {
    $dummy = [
        'site_name' => 'Workloop',
        'site_description' => 'Connect with freelancers worldwide (demo)',
        'site_email' => 'help@workloop.com',
        'site_phone' => '+91 2701932204',
        'platform_fee_percentage' => '20',
        'min_withdrawal_amount' => '100000',
        'maintenance_mode' => '0',
        'allow_registration' => '1',
    ];

    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    if (!$stmt) {
        // Debugging message
        error_log("MySQL Prepare failed: " . $conn->error);
        return $dummy[$key] ?? $default;
    }

    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row ? $row['setting_value'] : ($dummy[$key] ?? $default);
}


// Assign settings to variables
$site_name = getSetting($conn, 'site_name');
$site_description = getSetting($conn, 'site_description');
$site_email = getSetting($conn, 'site_email');
$site_phone = getSetting($conn, 'site_phone');
$platform_fee_percentage = getSetting($conn, 'platform_fee_percentage');
$min_withdrawal_amount = getSetting($conn, 'min_withdrawal_amount');
$maintenance_mode = getSetting($conn, 'maintenance_mode');
$allow_registration = getSetting($conn, 'allow_registration');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Settings - Workloop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <?php include("includes/sidebar.php"); ?>

    <!-- Main Content -->
    <div class="flex-1 p-8 ml-34 overflow-auto max-w-8xl">

        <!-- Session Messages -->
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-600 text-white rounded shadow">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-red-600 text-white rounded shadow">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Platform Settings</h1>
            <p class="text-gray-600 mt-1">Configure your platform's general settings and preferences.</p>
        </header>

        <form method="POST" class="space-y-8">

            <!-- Site Information -->
            <section class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Site Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                        <input type="text" id="site_name" name="site_name" 
                               value="<?= htmlspecialchars($site_name) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="site_email" class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                        <input type="email" id="site_email" name="site_email" 
                               value="<?= htmlspecialchars($site_email) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                        <textarea id="site_description" name="site_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($site_description) ?></textarea>
                    </div>
                    <div>
                        <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                        <input type="tel" id="site_phone" name="site_phone" 
                               value="<?= htmlspecialchars($site_phone) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </section>

            <!-- Payment Settings -->
            <section class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Payment Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="platform_fee_percentage" class="block text-sm font-medium text-gray-700 mb-2">Platform Fee (%)</label>
                        <input type="number" id="platform_fee_percentage" name="platform_fee_percentage" 
                               value="<?= htmlspecialchars($platform_fee_percentage) ?>" min="0" max="50" step="0.1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Fee charged on completed orders</p>
                    </div>
                    <div>
                        <label for="min_withdrawal_amount" class="block text-sm font-medium text-gray-700 mb-2">Minimum Withdrawal (₹)</label>
                        <input type="number" id="min_withdrawal_amount" name="min_withdrawal_amount" 
                               value="<?= htmlspecialchars($min_withdrawal_amount) ?>" min="1" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Minimum amount freelancers can withdraw</p>
                    </div>
                </div>
            </section>

            <!-- Platform Controls -->
            <section class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Platform Controls</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1"
                               <?= $maintenance_mode ? 'checked' : '' ?>
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="maintenance_mode" class="ml-3 text-sm font-medium text-gray-700">
                            Maintenance Mode
                        </label>
                        <p class="ml-2 text-xs text-gray-500">(Site will be unavailable to users)</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="allow_registration" name="allow_registration" value="1"
                               <?= $allow_registration ? 'checked' : '' ?>
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="allow_registration" class="ml-3 text-sm font-medium text-gray-700">
                            Allow New Registrations
                        </label>
                        <p class="ml-2 text-xs text-gray-500">(Users can create new accounts)</p>
                    </div>
                </div>
            </section>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-md hover:bg-indigo-700 font-semibold transition">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</body>
</html>
