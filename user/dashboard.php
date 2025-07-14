<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}
?>

<h2>Welcome, <?php echo $_SESSION['name']; ?> 👋</h2>
<img src="<?php echo $_SESSION['image']; ?>" width="100" style="border-radius:50%;">
<p>Email: <?php echo $_SESSION['email']; ?></p>
<a href="../auth/logout.php">Logout</a>
