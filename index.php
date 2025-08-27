

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
        <div class="card"><img src="assets/img1.jpg"><div class="card-body">Vibe Coding</div></div>
        <div class="card"><img src="assets/img2.jpg"><div class="card-body">Website Development</div></div>
        <div class="card"><img src="assets/img3.jpg"><div class="card-body">Video Editing</div></div>
        <div class="card"><img src="assets/img4.jpg"><div class="card-body">Software Support</div></div>
      </div>
    </div>

    <!-- Guides -->
    <div class="guide-section">
      <div class="guide-box"><h3>Start Your Side Hustle</h3><p>Learn how to turn skills into freelance income.</p></div>
      <div class="guide-box"><h3>Ecommerce Business Ideas</h3><p>Boost your brand by hiring experts online.</p></div>
      <div class="guide-box"><h3>Grow Social Presence</h3><p>Get help creating posts, ads, or campaigns.</p></div>
    </div>

    <!-- Top Freelancers -->
    <div class="featured-section">
      <h2>Top Freelancers</h2>
      <div class="featured-profiles">
        <div class="profile-card"><img src="assets/user1.jpg"><h4>Riya Sharma</h4><p>Web Developer</p><p>⭐⭐⭐⭐☆</p></div>
        <div class="profile-card"><img src="assets/user2.jpg"><h4>Aditya Mehta</h4><p>Video Editor</p><p>⭐⭐⭐⭐⭐</p></div>
        <div class="profile-card"><img src="assets/user3.jpg"><h4>Pooja Desai</h4><p>Logo Designer</p><p>⭐⭐⭐⭐☆</p></div>
      </div>
    </div>

    <!-- Promo -->
    <div class="promo-section" style="background:#eef7f4;">
      <h2>Premium Freelance Services for Businesses</h2>
      <p>Get access to top verified experts and reliable service with full project tracking.</p>
    </div>

    <!-- Testimonials -->
    <div class="testimonial-section">
      <h2>What Our Users Say</h2>
      <div class="testimonial"><p>“Workloop helped me find my first freelance job during college. It’s user-friendly and professional.”</p><strong>- Rahul, Student Freelancer</strong></div>
      <div class="testimonial"><p>“As a client, I loved the support and the quality of services I received. Great experience!”</p><strong>- Ayesha, Startup Owner</strong></div>
    </div>

    <!-- CTA -->
    <div class="cta-section">
      <h2>Freelance Services at Your Fingertips</h2>
      <button>Join Workloop Today</button>
    </div>

    <!-- Footer -->
    <div class="footer">
      &copy; 2025 Workloop. All rights reserved.
    </div>
    
  
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
