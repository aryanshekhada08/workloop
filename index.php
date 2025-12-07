

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Workloop | Freelance Services for Students</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  
    <link rel="stylesheet" href="style/index.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/auth.css">
    <?php $hideNavbarSearch = true; ?>
      <!-- PNG favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="assets/image/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="assets/image/favicon-16x16.png">
<link rel="icon" type="image/x-icon" href="assets/image/favicon.ico">

  </head>

  <body>
  <?php include "components/Navbar.php"; ?>
  <?php include "model.php"; ?>

    <!-- Hero Section -->
    <div class="hero-container">
      <video autoplay muted loop playsinline>
        <source src="assets/video/new_version.mp4" type="video/mp4">
      </video>
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h1>Our freelancers <span> <br> will take it from here</span></h1>
        
          <form method="GET" action="/workloop/client/search.php" class="search-wrapper">
            <input type="text" name="search" placeholder="Search for any service..." required />
            <button type="submit"><i class="fas fa-search"></i></button>
          </form>
        
        <div class="tag-buttons">
          <div class="tag">website development →</div>
          <div class="tag">architecture & interior →</div>
          <div class="tag">UGC videos →</div>
          <div class="tag">video editing →</div>
          <div class="tag">vibe coding → <span class="new">NEW</span></div>
        </div>
        <!-- <div class="trusted-by">Trusted by: Meta, Google, Netflix, PayPal, Payoneer</div> -->
        <div class="trusted-by-section">
    <div class="trusted-logos">
      <p class="trusted-label">Trusted by:</p>
      <img src="assets/image/logo/google.e74f4d9.svg" alt="Google" />
      <img src="assets/image/logo/meta.ff37dd3.svg" alt=" Meta" />
      <img src="assets/image/logo/netflix.b310314.svg" alt="Netflix" />
      <img src="assets/image/logo/paypal.d398de5.svg" alt="PayPal" />
      <img src="assets/image/logo/pg.22fca85.svg" alt="Payoneer" />
    </div>
  </div>

      </div>
    </div>

    <!-- Categories -->
    <?php
    include "show_category.php";
  ?>

    <!-- Popular Services -->
    <div class="popular-section">
  <h2>Popular Services</h2>
  <div class="cards">
    <div class="card">
      <img src="assets/download(1).png" alt="Vibe Coding">
      <div class="card-body">Vibe Coding</div>
    </div>
    <div class="card">
      <img src="assets/download(2).png" alt="Website Development">
      <div class="card-body">Website Development</div>
    </div>
    <div class="card">
      <img src="assets/download(3).png" alt="Video Editing">
      <div class="card-body">Video Editing</div>
    </div>
    <div class="card">
      <img src="assets/download(4).png" alt="Software Development">
      <div class="card-body">Software Development</div>
    </div>
    <div class="card">
      <img src="assets/download(5).png" alt="SEO">
      <div class="card-body">SEO</div>
    </div>
    <div class="card">
      <img src="assets/download.png" alt="Architecture & Interior Design">
      <div class="card-body">Architecture & Interior Design</div>
    </div>
  </div>
</div>


    <!-- Guides -->
<section class="guide-section">
  <div class="guide-box">
    <i class="fas fa-rocket"></i>
    <h3>Start Your Side Hustle</h3>
    <p>Learn how to turn skills into freelance income.</p>
  </div>
  <div class="guide-box">
    <i class="fas fa-store"></i>
    <h3>Ecommerce Business Ideas</h3>
    <p>Boost your brand by hiring experts online.</p>
  </div>
  <div class="guide-box">
    <i class="fas fa-bullhorn"></i>
    <h3>Grow Social Presence</h3>
    <p>Get help creating posts, ads, or campaigns.</p>
  </div>
</section>

<!-- Top Freelancers -->
<section class="featured-section">
  <h2>Top Freelancers</h2>
  <p class="subtitle">Meet some of the best talents ready to work for you</p>
  <div class="featured-profiles">
    <div class="profile-card">
      <img src="assets/pro2.png" alt="Riya Sharma">
      <h4>Riya Sharma</h4>
      <p class="role">Web Developer</p>
      <p class="rating">⭐️⭐️⭐️⭐️☆</p>
    </div>
    <div class="profile-card">
      <img src="assets/pro1.png" alt="Aditya Mehta">
      <h4>Aditya Mehta</h4>
      <p class="role">Video Editor</p>
      <p class="rating">⭐️⭐️⭐️⭐️⭐️</p>
    </div>
    <div class="profile-card">
      <img src="assets/pro3.png" alt="Pooja Desai">
      <h4>Pooja Desai</h4>
      <p class="role">Logo Designer</p>
      <p class="rating">⭐️⭐️⭐️⭐️☆</p>
    </div>
  </div>
</section>

<!-- Promo -->
<section class="promo-section">
  <div class="promo-content">
    <h2>Premium Freelance Services for Businesses</h2>
    <p>Get access to top verified experts and reliable service with full project tracking.</p>
  </div>
</section>

<!-- Testimonials -->
<section class="testimonial-section">
  <h2>What Our Users Say</h2>
  <div class="testimonials">
    <div class="testimonial">
      <p>“Workloop helped me find my first freelance job during college. It’s user-friendly and professional.”</p>
      <strong>- Rahul, Student Freelancer</strong>
    </div>
    <div class="testimonial">
      <p>“As a client, I loved the support and the quality of services I received. Great experience!”</p>
      <strong>- Ayesha, Startup Owner</strong>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <h2>Freelance Services at Your Fingertips</h2>
  <button class="cta-btn">Join Workloop Today</button>
</section>
    
  
<!-- Model is start -->
 <?php if (isset($_SESSION['show_role_modal']) && $_SESSION['show_role_modal'] && isset($_SESSION['user_id'])): ?>
  <script>
    const SHOW_ROLE_MODAL = true;
    const USER_ID = <?= json_encode($_SESSION['user_id']) ?>;
  </script>
  <?php unset($_SESSION['show_role_modal']); ?>
<?php else: ?>
  <script>
    const SHOW_ROLE_MODAL = false;
    const USER_ID = null;
  </script>
<?php endif; ?>

  <script src="auth-modal.js"></script>

  </body>
  </html>
 <?php include "components/footer.php"; ?>