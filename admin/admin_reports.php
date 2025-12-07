<?php
session_start();
require("../db.php");

// Check admin authentication
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../index.php");
//     exit();
// }

$statusFilter = $_GET['status'] ?? '';

// Handle status update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'], $_POST['new_status'])) {
    $report_id = (int)$_POST['report_id'];
    $new_status = $_POST['new_status'];
    $allowed_statuses = ['new', 'in_progress', 'resolved'];
    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE reports SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $new_status, $report_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Report status updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update report status.";
        }
        $stmt->close();
        header("Location: admin_reports.php?status=".urlencode($statusFilter));
        exit();
    }
}

// Fetch reports with optional status filter
$allowed_filters = ['new', 'in_progress', 'resolved'];
$whereSQL = "";
$params = [];
$paramTypes = "";

if (in_array($statusFilter, $allowed_filters)) {
    $whereSQL = " WHERE r.status = ? ";
    $params[] = $statusFilter;
    $paramTypes .= "s";
}

$sql = "
    SELECT r.*, u.name as user_name, u.email as user_email
    FROM reports r
    LEFT JOIN users u ON r.user_id = u.id
    $whereSQL
    ORDER BY r.created_at DESC
";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - User Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <?php include("../admin/includes/sidebar.php"); ?>

    <div class="flex-1 p-8 ml-34 max-w-8xl overflow-auto">
        <header class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h1 class="text-3xl font-bold text-gray-800">User Reports</h1>
            <form method="GET" class="flex items-center gap-3">
                <label for="statusFilter" class="font-semibold text-gray-700">Filter by status:</label>
                <select id="statusFilter" name="status" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="" <?= $statusFilter === '' ? 'selected' : '' ?>>All</option>
                    <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>New</option>
                    <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= $statusFilter === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
            </form>
        </header>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="mb-6 p-4 rounded bg-green-600 text-white font-semibold shadow">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-6 p-4 rounded bg-red-600 text-white font-semibold shadow">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Report ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">No reports found.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="align-top">
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['id']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['user_name'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['user_email'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['subject'] ?: '-') ?></td>
                                <td class="px-6 py-4 max-w-xl break-words whitespace-pre-wrap"><?= htmlspecialchars($row['message']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap capitalize font-semibold">
                                    <?php
                                    $statusColors = [
                                        'new' => 'text-yellow-600',
                                        'in_progress' => 'text-blue-600',
                                        'resolved' => 'text-green-600'
                                    ];
                                    $statusClass = $statusColors[$row['status']] ?? 'text-gray-600';
                                    ?>
                                    <span class="<?= $statusClass ?> uppercase"><?= htmlspecialchars($row['status']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= date('M d, Y H:i', strtotime($row['created_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" class="flex items-center gap-2">
                                        <input type="hidden" name="report_id" value="<?= (int)$row['id'] ?>">
                                        <select name="new_status" required
                                            class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <option value="new" <?= $row['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                            <option value="in_progress" <?= $row['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="resolved" <?= $row['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                        <button type="submit"
                                            class="ml-2 bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
