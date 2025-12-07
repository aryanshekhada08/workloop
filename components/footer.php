<head>
  <style>
    footer {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu,
        Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
      background-color: #1f2937; 
      color: #d1d5db; 
      padding: 2rem 1.5rem;
      margin-left: 0; 
    }
    footer{
      margin-left: 280px;
    }
    
    footer .footer-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 2rem;
    }

    footer .footer-column {
      flex: 1 1 250px;
      min-width: 200px;
    }

    footer h2,
    footer h3 {
      color: #f9fafb;
      font-weight: 700;
      margin-bottom: 1rem;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      font-size: 1rem;
    }

    footer a {
      color: #d1d5db;
      transition: color 0.25s ease-in-out;
      text-decoration: none;
      font-size: 0.9rem;
    }

    footer a:hover,
    footer a:focus {
      color: #22c55e;
      outline: none;
      text-decoration: underline;
    }

    footer p {
      margin-bottom: 1rem;
      line-height: 1.5;
    }

    footer ul {
      list-style: none;
      padding-left: 0;
      margin: 0;
    }

    footer ul li {
      margin-bottom: 0.75rem;
    }

    footer ul li:last-child {
      margin-bottom: 0;
    }

    footer .social-icons a {
      font-size: 1.5rem;
      margin-right: 1rem;
      transition: color 0.3s ease;
      display: inline-block;
    }

    footer .social-icons a:last-child {
      margin-right: 0;
    }

    footer .social-icons a:hover,
    footer .social-icons a:focus {
      color: #22c55e;
      cursor: pointer;
    }

    footer .copyright {
      border-top: 1px solid #374151;
      padding-top: 1.5rem;
      font-size: 0.8rem;
      color: #9ca3af;
      user-select: none;
      text-align: center;
      margin: 2rem auto 0;
      max-width: 1200px;
      letter-spacing: 0.04em;
    }

    @media (max-width: 768px) {
      footer .footer-container {
        flex-direction: column;
      }
      footer .footer-column {
        min-width: 100%;
      }
    }
  </style>
</head>

<footer>
  <div class="footer-container">
    <div class="footer-column about">
      <h2>Workloop</h2>
      <p>
        Workloop is your trusted platform connecting clients and freelancers worldwide,
        offering seamless workflow and secure payments.
      </p>
    </div>

    <div class="footer-column quick-links">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="dashboard.php" tabindex="0">Dashboard</a></li>
        <li><a href="orders.php" tabindex="0">My Orders</a></li>
        <li><a href="messages.php" tabindex="0">Messages</a></li>
        <li><a href="help_contact.php" tabindex="0">Help & Contact</a></li>
        <li><a href="profile.php" tabindex="0">Profile</a></li>
        <li><a href="logout.php" tabindex="0">Logout</a></li>
      </ul>
    </div>

    <div class="footer-column contact-social">
      <h3>Contact Us</h3>
      <p>
        <a href="mailto:support@workloop.com" tabindex="0" aria-label="Email support at support@workloop.com"
          >support@workloop.com</a><br />
        Phone: <a href="tel:+11234567890" tabindex="0" aria-label="Call phone number +1 123 456 7890"
          >+91 7201932204</a>
      </p>
      <div class="social-icons" aria-label="Social media links">
        <a href="#" aria-label="Facebook" title="Facebook" target="_blank" rel="noopener noreferrer" tabindex="0"
          ><i class="fab fa-facebook-f" aria-hidden="true"></i
        ></a>
        <a href="#" aria-label="Twitter" title="Twitter" target="_blank" rel="noopener noreferrer" tabindex="0"
          ><i class="fab fa-twitter" aria-hidden="true"></i
        ></a>
        <a href="#" aria-label="LinkedIn" title="LinkedIn" target="_blank" rel="noopener noreferrer" tabindex="0"
          ><i class="fab fa-linkedin-in" aria-hidden="true"></i
        ></a>
        <a href="#" aria-label="Instagram" title="Instagram" target="_blank" rel="noopener noreferrer" tabindex="0"
          ><i class="fab fa-instagram" aria-hidden="true"></i
        ></a>
      </div>
    </div>
  </div>

  <div class="copyright">
    &copy; <?= date('Y') ?> Workloop. All rights reserved.
  </div>
</footer>

<!-- Font Awesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
