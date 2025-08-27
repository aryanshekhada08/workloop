<?php
session_start();
require("../db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch user reviews with client names
$reviewsQuery = $conn->prepare("
    SELECT r.rating, r.review_text AS comment, r.created_at, u.name as reviewer
    FROM reviews r
    JOIN users u ON r.client_id = u.id
    WHERE r.freelancer_id = ?
    ORDER BY r.created_at DESC
");
$reviewsQuery->bind_param("i", $user_id);
$reviewsQuery->execute();
$reviews = $reviewsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php include("../components/sidebar.php"); ?>
<?php include("../components/Navbar.php"); ?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Freelancer Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-sans">

<div class="max-w-5xl mx-auto mt-10 mr-20 p-6 bg-white shadow-xl rounded-lg">

  <!-- Profile Header -->
  <div class="flex items-center space-x-6 border-b pb-6">
    <img id="profilePreview" src="../assets/image/user/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>"
         alt="Profile" 
         class="w-28 h-28 rounded-full object-cover border-4 border-indigo-500 shadow-md">
    <div class="flex-1">
      <h2 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($user['name']) ?></h2>
      <p class="text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
      <p class="text-sm text-gray-400">🎉 Joined: <?= date("d M Y", strtotime($user['created_at'])) ?></p>
      <p class="mt-2 text-gray-700 italic"><?= nl2br(htmlspecialchars($user['bio'] ?? 'No bio added yet.')) ?></p>
    </div>
    <div>
      <a href="edit_profile.php" 
         class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
        Edit Profile
      </a>
    </div>
  </div>

  <!-- Skills Section -->
  <div class="mt-10">
    <h3 class="text-2xl font-bold text-gray-800 border-b pb-2">🛠 My Skills</h3>
    <div class="mt-4 flex flex-wrap gap-2">
      <?php 
        $skills_arr = array_filter(array_map('trim', explode(',', $user['skills'] ?? '')));
        if (count($skills_arr) > 0):
          foreach ($skills_arr as $skill): ?>
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm"><?= htmlspecialchars($skill) ?></span>
          <?php endforeach; 
        else: ?>
          <p class="text-gray-500 italic">No skills added yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Reviews Section -->
  <div class="mt-10">
    <h3 class="text-2xl font-bold text-gray-800 border-b pb-2">⭐ Client Reviews</h3>
    <?php if (count($reviews) > 0): ?>
      <div class="mt-4 space-y-4">
        <?php foreach ($reviews as $review): ?>
          <div class="p-4 border rounded-lg shadow-sm bg-gray-50">
            <div class="flex justify-between items-center">
              <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($review['reviewer']) ?></h4>
              <div class="text-yellow-500">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <?= $i <= $review['rating'] ? "★" : "☆" ?>
                <?php endfor; ?>
              </div>
            </div>
            <p class="text-gray-600 mt-2"><?= htmlspecialchars($review['comment']) ?></p>
            <small class="text-gray-400">Reviewed on <?= date("d M Y", strtotime($review['created_at'])) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="mt-4 text-gray-500 italic">No reviews yet.</p>
    <?php endif; ?>
  </div>

</div>

<script>
const changeBtn = document.getElementById('changePhotoBtn');
const fileInput = document.getElementById('profileUpload');
const profileImage = document.getElementById('profileImage');

changeBtn?.addEventListener('click', () => fileInput.click());
fileInput?.addEventListener('change', (e) => {
  const file = e.target.files[0];
  if(file) {
    profileImage.src = URL.createObjectURL(file);
  }
});
</script>

</body>
</html>
