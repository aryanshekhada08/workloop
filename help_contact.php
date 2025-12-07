<?php
session_start();
require("../workloop/db.php");

// Require logged-in user (client or freelancer)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['client', 'freelancer'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$subject = "";
$message = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($message)) {
        $errors[] = "Please enter your message or report.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO reports (user_id, user_role, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $user_role, $subject, $message);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Your report has been submitted successfully. We will get back to you shortly.";
            header("Location: help_contact.php");
            exit();
        } else {
            $errors[] = "Failed to submit your report. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Help & Contact Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<?php include("../workloop/components/Navbar.php"); ?>
<?php include("../workloop/components/sidebar.php"); ?>

<div class="max-w-3xl mx-auto my-12 p-6 bg-white rounded-lg shadow">

    <h1 class="text-3xl font-semibold mb-6">Help & Contact Admin</h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded font-medium">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded font-medium">
            <ul class="list-disc px-5">
                <?php foreach($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate class="space-y-6">
        <label for="subject" class="block font-medium">Subject (optional)</label>
        <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($subject) ?>"
               class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" />

        <label for="message" class="block font-medium">Message / Report <span class="text-red-600">*</span></label>
        <textarea id="message" name="message" rows="6" required
                  class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  placeholder="Describe your issue or report here"><?= htmlspecialchars($message) ?></textarea>

        <button type="submit"
                class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700 font-semibold transition">
            Submit
        </button>
    </form>
</div>

</body>
</html>
