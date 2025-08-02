<?php
session_start();
require("../db.php");

// Check if client is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$chat_with = $_GET['user_id'] ?? 0;

// Get receiver's name
$receiver_name = "Unknown";
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $chat_with);
$stmt->execute();
$stmt->bind_result($receiver_name);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat with <?= htmlspecialchars($receiver_name) ?></title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<!-- Include Navbar -->
<?php include '../components/navbar.php'; ?>
<!-- Include Sidebar -->
<?php include '../components/sidebar.php'; ?>

<div class="md:ml-64 p-6">
  <div class="max-w-3xl mx-auto bg-white rounded shadow overflow-hidden">

    <!-- Header -->
    <div class="bg-blue-600 text-white px-6 py-4 text-lg font-semibold">
      Chat with <?= htmlspecialchars($receiver_name) ?>
    </div>

    <!-- Chat Messages -->
    <div id="chatBox" class="h-96 overflow-y-auto px-4 py-3 bg-gray-50 space-y-2 text-sm">
      <!-- Messages will load here -->
    </div>

    <!-- Message Form -->
    <form id="messageForm" class="flex items-center gap-3 border-t px-4 py-3 bg-white">
      <input type="hidden" name="sender_id" value="<?= $user_id ?>">
      <input type="hidden" name="receiver_id" value="<?= $chat_with ?>">

      <input 
        type="text" 
        name="message" 
        placeholder="Type a message..." 
        class="flex-grow rounded-full border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" 
        required
      >

      <button 
        type="submit" 
        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-full transition-all duration-200"
      >
        Send
      </button>
    </form>
  </div>
</div>

<script>
function fetchMessages() {
  $.post("/workloop/chat/fetch_messages.php", {
    sender_id: <?= $user_id ?>,
    receiver_id: <?= $chat_with ?>
  }, function(data) {
    $('#chatBox').html(data);
    $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
  });
}

setInterval(fetchMessages, 1500);

$('#messageForm').submit(function(e) {
  e.preventDefault();
  $.post("/workloop/chat/send_message.php", $(this).serialize(), function() {
    $('input[name="message"]').val('');
    fetchMessages();
  });
});
</script>

</body>
</html>
    