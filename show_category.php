<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <style>
       .category-box {
        min-width: 160px;
        min-height: 140px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        margin-left:10px;
}

.category-box:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
   background: linear-gradient(135deg, #5feea4ea);

}

.category-icon {
  height: 40px;
  margin-bottom: 10px;
  transition: filter 0.3s ease;
}

.category-box:hover .category-icon {
  filter: brightness(0) invert(1); /* Makes icon white on green background */
}

.category-name {
  font-weight: 500;
  font-size: 14px;
  text-align: center;
  transition: color 0.3s ease;
}

.categories {
  display: flex;
  gap: 15px;
  overflow-x: auto;
  padding: 20px 0;
}

.categories::-webkit-scrollbar {
  display: none;
}

     </style>
</head>
<body>
    
</body>
</html>
<?php
include 'db.php';
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

  <div class="container">
    <h2>Popular Categories</h2>
    <div class="categories ">
      <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
        <div class="category-box ">
          <img src="./<?= $cat['icon'] ?>" class="category-icon " style="height: 40px;" alt="">
          <div class="category-name"><?= htmlspecialchars($cat['name']) ?></div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>