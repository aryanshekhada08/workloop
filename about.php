<?php include(__DIR__ . "/components/Navbar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Workloop</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    .gradient-bg {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .card-hover {
      transition: all 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-5px);
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Hero Section -->
  <section class="gradient-bg text-white py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-6 text-center">
      <h1 class="text-6xl font-bold mb-6 leading-tight">About Workloop</h1>
      <p class="text-xl max-w-3xl mx-auto mb-8 leading-relaxed">
        Connecting talented freelancers with ambitious clients worldwide. Building the future of remote work, one project at a time.
      </p>
      <div class="flex flex-wrap justify-center gap-4 text-sm font-semibold">
        <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full">🌍 Global Platform</span>
        <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full">🔒 Secure Payments</span>
        <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full">⭐ Quality Guaranteed</span>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-6">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div class="p-6">
          <div class="text-4xl font-bold text-green-600 mb-2">50K+</div>
          <div class="text-gray-600 font-medium">Active Freelancers</div>
        </div>
        <div class="p-6">
          <div class="text-4xl font-bold text-green-600 mb-2">25K+</div>
          <div class="text-gray-600 font-medium">Happy Clients</div>
        </div>
        <div class="p-6">
          <div class="text-4xl font-bold text-green-600 mb-2">100K+</div>
          <div class="text-gray-600 font-medium">Projects Completed</div>
        </div>
        <div class="p-6">
          <div class="text-4xl font-bold text-green-600 mb-2">150+</div>
          <div class="text-gray-600 font-medium">Countries Served</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Mission & Vision -->
  <section class="py-20 px-6">
    <div class="max-w-6xl mx-auto">
      <div class="grid md:grid-cols-2 gap-16 items-center mb-20">
        <div>
          <h2 class="text-4xl font-bold text-gray-800 mb-6">Our Mission</h2>
          <p class="text-lg leading-relaxed text-gray-700 mb-6">
            At Workloop, we believe that talent knows no boundaries. Our mission is to create a seamless, 
            trustworthy platform where skilled professionals can showcase their expertise and businesses 
            can find exactly what they need to grow.
          </p>
          <p class="text-lg leading-relaxed text-gray-700">
            We're not just connecting people – we're building careers, growing businesses, and creating 
            opportunities that transform lives across the globe.
          </p>
        </div>
        <div class="relative">
          <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80" 
               alt="Our Mission" class="rounded-2xl shadow-2xl">
          <div class="absolute -bottom-6 -right-6 bg-green-600 text-white p-6 rounded-xl shadow-lg">
            <i class="fas fa-handshake text-3xl"></i>
          </div>
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-16 items-center">
        <div class="md:order-2">
          <h2 class="text-4xl font-bold text-gray-800 mb-6">Our Vision</h2>
          <p class="text-lg leading-relaxed text-gray-700 mb-6">
            We envision a world where geographical barriers don't limit professional opportunities. 
            A world where the best talent can work with the best companies, regardless of location.
          </p>
          <p class="text-lg leading-relaxed text-gray-700">
            Through innovation, transparency, and unwavering commitment to quality, we're building 
            the future of work – one that's flexible, inclusive, and empowering for everyone.
          </p>
        </div>
        <div class="md:order-1 relative">
          <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=800&q=80" 
               alt="Our Vision" class="rounded-2xl shadow-2xl">
          <div class="absolute -bottom-6 -left-6 bg-blue-600 text-white p-6 rounded-xl shadow-lg">
            <i class="fas fa-rocket text-3xl"></i>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Core Values -->
  <section class="bg-gray-100 py-20 px-6">
    <div class="max-w-6xl mx-auto">
      <h2 class="text-4xl font-bold text-center text-gray-800 mb-16">Our Core Values</h2>
      <div class="grid md:grid-cols-3 gap-10">
        <div class="bg-white p-8 rounded-2xl shadow-lg card-hover text-center">
          <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-shield-alt text-2xl text-green-600"></i>
          </div>
          <h3 class="text-2xl font-bold mb-4 text-gray-800">Trust & Security</h3>
          <p class="text-gray-600 leading-relaxed">
            We prioritize secure transactions, verified profiles, and transparent processes to ensure 
            every interaction is safe and trustworthy.
          </p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-lg card-hover text-center">
          <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-star text-2xl text-blue-600"></i>
          </div>
          <h3 class="text-2xl font-bold mb-4 text-gray-800">Quality Excellence</h3>
          <p class="text-gray-600 leading-relaxed">
            We maintain high standards through rigorous vetting, skill assessments, and continuous 
            feedback systems to ensure top-quality deliverables.
          </p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-lg card-hover text-center">
          <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-users text-2xl text-purple-600"></i>
          </div>
          <h3 class="text-2xl font-bold mb-4 text-gray-800">Community First</h3>
          <p class="text-gray-600 leading-relaxed">
            Our platform is built by the community, for the community. We listen, adapt, and grow 
            together with our users' needs and feedback.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Why Choose Workloop -->
  <section class="py-20 px-6">
    <div class="max-w-6xl mx-auto">
      <h2 class="text-4xl font-bold text-center text-gray-800 mb-16">Why Choose Workloop?</h2>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
        <div class="flex flex-col items-center text-center p-6">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-clock text-2xl text-green-600"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3 text-gray-800">Fast Matching</h3>
          <p class="text-gray-600">AI-powered matching connects you with the right talent or projects in minutes, not days.</p>
        </div>

        <div class="flex flex-col items-center text-center p-6">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-credit-card text-2xl text-blue-600"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3 text-gray-800">Secure Payments</h3>
          <p class="text-gray-600">Escrow protection and multiple payment options ensure safe, timely transactions.</p>
        </div>

        <div class="flex flex-col items-center text-center p-6">
          <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-headset text-2xl text-purple-600"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3 text-gray-800">24/7 Support</h3>
          <p class="text-gray-600">Our dedicated support team is always ready to help resolve any issues quickly.</p>
        </div>

        <div class="flex flex-col items-center text-center p-6">
          <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-chart-line text-2xl text-yellow-600"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3 text-gray-800">Growth Tools</h3>
          <p class="text-gray-600">Analytics, portfolio builders, and skill assessments help you grow your career.</p>
        </div>

        <div class="flex flex-col items-center text-center p-6">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-globe text-2xl text-red-600"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3 text-gray-800">Global Reach</h3>
          <p class="text-gray-600">Work with clients and freelancers from 150+ countries around the world.</p>
        </div>

        <div class="flex flex-col items-center text-center p-6">
          <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-award text-2xl text-indigo-600"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3 text-gray-800">Quality Assurance</h3>
          <p class="text-gray-600">Verified profiles, skill tests, and review systems ensure top-quality work.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="gradient-bg py-20 text-white text-center">
    <div class="max-w-4xl mx-auto px-6">
      <h2 class="text-4xl font-bold mb-6">Ready to Join the Future of Work?</h2>
      <p class="text-xl mb-10 leading-relaxed">
        Whether you're a talented freelancer looking to grow your career or a business seeking exceptional talent, 
        Workloop is your gateway to success.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="register.php?role=freelancer" 
           class="px-8 py-4 bg-white text-green-600 font-bold rounded-xl shadow-lg hover:bg-gray-100 transition inline-flex items-center justify-center">
          <i class="fas fa-user-tie mr-2"></i>
          Join as Freelancer
        </a>
        <a href="register.php?role=client" 
           class="px-8 py-4 bg-green-800 text-white font-bold rounded-xl shadow-lg hover:bg-green-900 transition inline-flex items-center justify-center">
          <i class="fas fa-briefcase mr-2"></i>
          Hire Talent
        </a>
      </div>
    </div>
  </section>

<?php include(__DIR__ . "/components/footer.php"); ?>

</body>
</html>
