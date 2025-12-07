<?php
session_start();
require("../db.php");

// Ensure logged in and role is client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Define your default profile image filename here (adjust as needed)
$defaultImage = "default.png";

// Query to get freelancer contacts with last message info for this client
$sql = "
SELECT u.id AS freelancer_id, u.name AS freelancer_name, u.email AS freelancer_email, u.profile_image,
       m.message AS last_message, m.sent_at AS last_message_time
FROM users u
JOIN (
   SELECT 
     CASE
       WHEN sender_id = ? THEN receiver_id
       ELSE sender_id
     END AS chat_partner_id,
     MAX(sent_at) AS last_message_time
   FROM messages
   WHERE sender_id = ? OR receiver_id = ?
   GROUP BY chat_partner_id
) last_msgs ON u.id = last_msgs.chat_partner_id
JOIN messages m ON 
   ((m.sender_id = ? AND m.receiver_id = u.id) OR (m.sender_id = u.id AND m.receiver_id = ?))
   AND m.sent_at = last_msgs.last_message_time
WHERE u.role = 'freelancer'
ORDER BY m.sent_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $client_id, $client_id, $client_id, $client_id, $client_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Messages - Client</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-100 min-h-screen">
    <?php include("../components/sidebar.php"); ?>
    <?php include("../components/Navbar.php"); ?>

    <main class="ml-80 p-6 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Messages</h1>

        <?php if ($result->num_rows > 0): ?>
            <ul class="divide-y divide-gray-200 bg-white rounded shadow-md">
                <?php while ($row = $result->fetch_assoc()):
                    $snippet = strlen($row['last_message']) > 50 ? substr($row['last_message'], 0, 47) . "..." : $row['last_message'];
                    $imgPath = "../assets/image/user/" . $row['profile_image'];
                    // Check if image exists AND is not default image
                    $hasImage = (!empty($row['profile_image']) && 
                                 $row['profile_image'] !== $defaultImage && 
                                 file_exists($imgPath));
                ?>
                <li>
                    <a href="chat.php?user_id=<?= $row['freelancer_id'] ?>" class="block p-4 hover:bg-green-50 flex justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <?php if ($hasImage): ?>
                                <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($row['freelancer_name']) ?>"
                                     class="w-10 h-10 rounded-full object-cover flex-shrink-0" />
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-green-700 flex items-center justify-center text-white font-semibold select-none">
                                    <?= strtoupper(substr($row['freelancer_name'], 0, 2)) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <p class="text-gray-900 font-semibold"><?= htmlspecialchars($row['freelancer_name']) ?></p>
                                <p class="text-gray-700 text-sm"><?= htmlspecialchars($snippet) ?></p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 whitespace-nowrap">
                            <?= date("M d, H:i", strtotime($row['last_message_time'])) ?>
                        </div>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">No conversations found.</p>
        <?php endif; ?>
    </main>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>
