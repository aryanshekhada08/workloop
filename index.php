<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Workloop | Freelance Services for Students</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="style/index.css">
  <link rel="stylesheet" href="style/navbar.css">
  <link rel="stylesheet" href="style/auth.css">
  <?php $hideNavbarSearch = true; ?>
</head>

<body>
 <?php include "components/Navbar.php"; ?>

 <div id="authModal" class="modal">
  <div class="modal-content">
    
    <div class="modal-left">
      <h2>Success starts here</h2>
      <ul>
        <li>✔ Over 700 categories</li>
        <li>✔ Quality work done faster</li>
        <li>✔ Access to global talent</li>
      </ul>
    </div>
    <div class="modal-right">
       <?php
if (isset($_SESSION['auth_error'])) {
    echo '<p style="color: red;">' . $_SESSION['auth_error'] . '</p>';
    unset($_SESSION['auth_error']);
}
if (isset($_SESSION['auth_success'])) {
    echo '<p style="color: green;">' . $_SESSION['auth_success'] . '</p>';
    unset($_SESSION['auth_success']);
}
?>
      <span id="closeModalBtn" class="close">&times;</span>
      <h2 id="formTitle">Login</h2>
      <form id="authForm" action="login.php" method="POST">
        <input type="text" name="name" placeholder="Full Name" style="display:none;" />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Continue</button>
      </form>
      <p id="toggleForm" style="margin-top: 15px;">
        Don’t have an account? <a href="#" onclick="openModal('signup')">Sign up</a>
      </p>
     

    </div>
  </div>
</div>

  <!-- Hero Section -->
  <div class="hero-container">
    <video autoplay muted loop playsinline>
      <source src="assets/video/new_version.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>Our freelancers <span> <br> will take it from here</span></h1>
      <div class="search-wrapper">
        <input type="text" placeholder="Search for any service..." />
        <button><i class="fas fa-search"></i></button>
      </div>
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
  <div class="categories">
    <div class="category-box">Logo Design</div>
    <div class="category-box">WordPress</div>
    <div class="category-box">Voice Over</div>
    <div class="category-box">AI Artist</div>
    <div class="category-box">Social Media</div>
    <div class="category-box">SEO</div>
    <div class="category-box">Video Editing</div>
  </div>

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


  

  <script src="auth-modal.js"></script>
<?php if (isset($_SESSION['auth_type'])): ?>
  <script>
    window.onload = () => {
      openModal('<?php echo $_SESSION['auth_type']; ?>');
    }
  </script>
  <?php unset($_SESSION['auth_type']); ?>
<?php endif; ?>

</body>
</html>
