<?php
require '../db.php';

$q = trim($_GET['q'] ?? '');
if ($q === '') exit;

$stmt = $conn->prepare("SELECT name FROM users WHERE name LIKE ? LIMIT 10");
$like = "%$q%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div class='px-4 py-2 hover:bg-gray-100 cursor-pointer'>" . htmlspecialchars($row['name']) . "</div>";
}
?>
