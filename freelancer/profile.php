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
$stmt->close();

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
$reviewsQuery->close();

// Get average rating and total reviews from users table
$freelancer_id = $user_id;

if ($freelancer_id > 0) {
    $stmt = $conn->prepare("SELECT rating, total_reviews FROM users WHERE id = ?");
    $stmt->bind_param("i", $freelancer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ratingData = $result->fetch_assoc();
    $stmt->close();

    $averageRating = floatval($ratingData['rating'] ?? 0);
    $totalReviews = intval($ratingData['total_reviews'] ?? 0);
} else {
    $averageRating = 0;
    $totalReviews = 0;
}

?>

<?php include("../components/sidebar.php"); ?>
<?php include("../components/Navbar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Freelancer Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-sans">

<div class="max-w-5xl mx-auto mt-10 mr-45 p-6 bg-white shadow-xl rounded-lg">

    <!-- Profile Header -->
    <div class="flex items-center space-x-6 border-b pb-6">
        <img id="profilePreview" src="../assets/image/user/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>"
             alt="Profile"
             class="w-28 h-28 rounded-full object-cover border-4 border-indigo-500 shadow-md">
        <div class="flex-1">
            <h2 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($user['name']) ?></h2>
            <p class="text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
            <p class="text-sm text-gray-400">🎉 Joined: <?= date("d M Y", strtotime($user['created_at'])) ?></p>
            <p class="mt-2 text-gray-700 italic whitespace-pre-wrap"><?= nl2br(htmlspecialchars($user['bio'] ?? 'No bio added yet.')) ?></p>
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

    <!-- Freelancer Reviews Section -->
    <div class="mt-10 max-w-3xl mx-auto">
        <h3 class="text-2xl font-bold text-gray-800 border-b pb-2 mb-6">⭐ Client Reviews for Your Work</h3>

        <!-- Average Rating with stars and total number of reviews -->
        <div class="mb-4 flex items-center space-x-3 max-w-3xl mx-auto">
            <div class="text-yellow-400 text-2xl" aria-label="Average rating: <?= htmlspecialchars($averageRating) ?>">
                <?php
                $fullStars = floor($averageRating);
                $halfStar = ($averageRating - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                // Full stars
                for ($i = 0; $i < $fullStars; $i++):
                    echo "&#9733;"; // full star
                endfor;

                // Half star (shown by partially colored star)
                if ($halfStar):
                    echo '<span style="position: relative; display: inline-block; width: 1em; color: #fbbf24;">&#9733;<span style="position: absolute; left: 0; width: 50%; overflow: hidden; color: #d1d5db;">&#9733;</span></span>';
                endif;

                // Empty stars
                for ($i = 0; $i < $emptyStars; $i++):
                    echo "&#9734;"; // empty star
                endfor;
                ?>
            </div>

            <div class="text-gray-700 text-lg font-semibold">
                <?= number_format($averageRating, 1) ?> (<?= intval($totalReviews) ?> Review<?= intval($totalReviews) !== 1 ? 's' : '' ?>)
            </div>
        </div>

        <!-- Individual reviews listing -->
        <?php if (!empty($reviews)): ?>
            <div class="space-y-6">
                <?php foreach ($reviews as $review): ?>
                    <div class="p-5 border rounded-lg shadow bg-white">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($review['reviewer']) ?></h4>
                            <div class="text-yellow-400 text-lg" aria-label="Rating: <?= intval($review['rating']) ?> out of 5 stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= $review['rating'] ? "&#9733;" : "&#9734;" ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($review['comment']) ?></p>
                        <div class="text-gray-400 text-sm mt-3">
                            Reviewed on <?= date("d M Y", strtotime($review['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mt-4 text-gray-500 italic text-center">No reviews received yet. Encourage clients to leave feedback!</p>
        <?php endif; ?>
    </div>

</div>

<script>
    // Profile image preview script (optional)
    const changeBtn = document.getElementById('changePhotoBtn');
    const fileInput = document.getElementById('profileUpload');
    const profileImage = document.getElementById('profileImage');

    if(changeBtn && fileInput && profileImage){
        changeBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if(file) {
                profileImage.src = URL.createObjectURL(file);
            }
        });
    }
</script>

</body>
</html>
