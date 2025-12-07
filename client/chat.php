<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$chat_with = intval($_GET['user_id'] ?? 0);
if ($chat_with <= 0) {
    echo "Invalid chat user.";
    exit();
}

$receiver_name = "Unknown";
$receiver_image = null;

$stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $chat_with);
$stmt->execute();
$stmt->bind_result($receiver_name, $receiver_image);
$stmt->fetch();
$stmt->close();

function initials($name) {
    $words = explode(" ", $name);
    $initials = "";
    foreach ($words as $w) {
        $initials .= strtoupper($w[0]);
    }
    return substr($initials, 0, 2);
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <title>Chat with <?= htmlspecialchars($receiver_name) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #chatBox::-webkit-scrollbar {
            width: 8px;
        }
        #chatBox::-webkit-scrollbar-thumb {
            background-color: #a0aeb3;
            border-radius: 6px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <?php include '../components/Navbar.php'; ?>
    <div class="flex flex-1 overflow-hidden">
        <?php include '../components/sidebar.php'; ?>

        <main class="flex flex-col flex-grow max-w-3xl mx-auto bg-white rounded-lg shadow-md h-screen md:h-[calc(100vh-64px)]">
            <!-- Header -->
            <header class="flex items-center justify-between bg-[#1DBF73] text-white p-4 rounded-t-lg shadow-md gap-4 select-none">
                <button onclick="window.location.href='messages.php'" aria-label="Back to messages list"
                    class="flex items-center gap-2 bg-green-800 hover:bg-green-900 px-3 py-1 rounded text-sm whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </button>

                <div class="flex items-center gap-3 flex-grow min-w-0">
                    <?php if ($receiver_image && file_exists("../assets/image/user/" . $receiver_image)): ?>
                        <img src="../assets/image/user/<?= htmlspecialchars($receiver_image) ?>" alt="Profile Image"
                            class="h-10 w-10 rounded-full object-cover flex-shrink-0" />

                    <?php else: ?>
                        <div class="h-10 w-10 rounded-full bg-green-800 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                            <?= initials($receiver_name) ?>
                        </div>
                    <?php endif; ?>
                    <h2 class="font-semibold text-xl truncate"><?= htmlspecialchars($receiver_name) ?></h2>
                </div>

                <div style="width: 60px;"></div>
            </header>

            <!-- Chat Messages -->
            <section id="chatBox"
                class="flex-grow overflow-auto p-6 bg-gray-50 space-y-5 scrollbar-thin scrollbar-thumb-gray-900 scrollbar-track-gray-200">
                <!-- Messages populated dynamically -->
            </section>

            <!-- Message Input -->
            <form id="messageForm" class="flex items-center border-t px-5 py-4 bg-white rounded-b-lg">
                <input type="hidden" name="sender_id" value="<?= $user_id ?>">
                <input type="hidden" name="receiver_id" value="<?= $chat_with ?>">

                <input type="text" name="message" autocomplete="off" placeholder="Type your message..." required
                    class="flex-grow rounded-full border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-700 transition" />

                <button type="submit"
                    class="ml-4 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-full font-semibold transition duration-150">
                    Send
                </button>
            </form>
        </main>
    </div>

    <script>
        const userId = <?= $user_id ?>;
        const chatWith = <?= $chat_with ?>;
        const chatBox = document.getElementById('chatBox');
        const messageForm = document.getElementById('messageForm');
        const messageInput = messageForm.querySelector('input[name="message"]');

        let firstLoad = true;

        function createMessageBubble(text, time, isSender, senderName) {
            const initials = senderName.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
            const justifyClass = isSender ? 'justify-end' : 'justify-start';
            const bubbleClass = isSender ? 'bg-[#1DBF73] text-white' : 'bg-white border border-gray-300';
            const textAlign = isSender ? 'text-right' : 'text-left';

         return `
            <div class="flex items-start ${justifyClass} max-w-[98%]">
                ${isSender ? '' : `<div class="h-9 w-9 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-lg select-none">${initials}</div>`}
                <div>
                    <div class="rounded-lg px-5 py-3 ${bubbleClass} shadow break-words">${text}</div>
                    <div class="text-xs text-gray-900 mt-1 ${textAlign}">${time}</div>
                </div>
                ${isSender ? `<div class="h-9 w-9 rounded-full bg-green-900 flex items-center justify-center text-white font-bold text-lg select-none">${initials}</div>` : ''}
            </div>`;

        }

        async function fetchMessages() {
            try {
                const formData = new FormData();
                formData.append('sender_id', userId);
                formData.append('receiver_id', chatWith);

                const response = await fetch('../chat/fetch_messages.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    console.error('Network response was not ok.');
                    return;
                }

                const messages = await response.json();

                if (messages.length === 0) {
                    chatBox.innerHTML = `<div class="text-center text-gray-500 mt-10">No messages found</div>`;
                    return;
                }

                let html = '';
                messages.forEach(msg => {
                    const isSender = msg.sender_id == userId;
                    const date = new Date(msg.sent_at);
                    const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    html += createMessageBubble(msg.message, timeStr, isSender, msg.sender_name);
                });

                chatBox.innerHTML = html;

                if (firstLoad) {
                    chatBox.scrollTop = 0; // Show oldest messages on first load
                    firstLoad = false;
                    //  chatBox.scrollTop = chatBox.scrollHeight;
                }
                // No scroll changes on periodic fetches

            } catch (error) {
                console.error('Fetch messages error:', error);
            }
        }

        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            const formData = new FormData(messageForm);

            try {
                const response = await fetch('/workloop/chat/send_message.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    alert('Failed to send message.');
                    return;
                }

                messageInput.value = '';
                await fetchMessages();
                chatBox.scrollTop = chatBox.scrollHeight; // Scroll to bottom after sending message

            } catch (error) {
                alert('Failed to send message.');
                console.error('Send message error:', error);
            }
        });

        setInterval(fetchMessages, 1500);
        fetchMessages();
    </script>
</body>

</html>
