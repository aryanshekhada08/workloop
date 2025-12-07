<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Popular Categories</title>
  <style>
    /* Reset + Base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      background: #f9fafc;
      color: #333;
      line-height: 1.6;
    }

    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }

    h2 {
      font-size: 22px;
      font-weight: 600;
      margin-bottom: 20px;
     
      color: #222;
    }
    .h21{
          margin-left: 490px;
    }

    /* Category wrapper */
    .categories {
      display: flex;
      gap: 15px;
      overflow-x: auto;
      padding: 10px 0;
      scroll-behavior: smooth;
    }

    .categories::-webkit-scrollbar {
      height: 6px;
    }

    .categories::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 10px;
    }

    /* Category box */
    .category-box {
      flex: 0 0 auto;
      min-width: 150px;
      min-height: 140px;
      background: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 16px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 16px;
      transition: all 0.3s ease;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
      cursor: pointer;
    }

    .category-box:hover {
      transform: translateY(-6px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
      background: linear-gradient(135deg, #4cafef, #43e695);
    }

    .category-icon {
      height: 48px;
      margin-bottom: 12px;
      transition: filter 0.3s ease;
    }

    .category-box:hover .category-icon {
      filter: brightness(0) invert(1);
    }

    .category-name {
      font-weight: 500;
      font-size: 15px;
      text-align: center;
      transition: color 0.3s ease;
    }

    .category-box:hover .category-name {
      color: #fff;
    }
  </style>
</head>
<body>

  <?php
  include 'db.php';
  $categories = mysqli_query($conn, "SELECT * FROM categories");
  ?>

  <div class="container">
    <h2 class="h21">Popular Categories</h2>
    <div class="categories">
      <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
        <div class="category-box">
          <img src="./<?= $cat['icon'] ?>" class="category-icon" alt="">
          <div class="category-name"><?= htmlspecialchars($cat['name']) ?></div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

</body>
</html>
