<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy - Bhaktivedanta Gurukul School of Excellence</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="images/bvgLogo.png" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* === Custom Colors === */
    .maroon-bg { background-color: #800000; }
    .maroon-text { color: #800000; }
    .brown-text { color: #3e2723; }
    .blue-text { color: #003366; }
    .maroon-border { border-color: #800000; }

    /* === Animations === */
    .content-section { transition: all 0.3s ease; }
    .content-section:hover { transform: translateX(5px); }

    .back-to-top {
      position: fixed;
      bottom: 20px;
      right: 20px;
      display: none;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800">

  <!-- Header -->
  <?php include 'includes/header.php'; ?>

  <header class="bg-white shadow-sm maroon-border border-b-4">
    <div class="container mx-auto px-4 py-6">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 maroon-bg rounded-lg flex items-center justify-center">
            <i class="fas fa-shield-alt text-white"></i>
          </div>
          <h1 class="text-2xl font-bold maroon-text">Privacy Policy</h1>
        </div>
        <div class="text-sm text-gray-500">
          Last Updated: <span id="lastUpdated"></span>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
      
      <!-- Table of Contents -->
      <div class="bg-blue-50 p-6 border-b-2 maroon-border">
        <h2 class="text-lg font-semibold mb-3 maroon-text">Table of Contents</h2>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
          <li><a href="#info-we-collect" class="blue-text hover:underline">1. Information We Collect</a></li>
          <li><a href="#how-we-use" class="blue-text hover:underline">2. How We Use Your Information</a></li>
          <li><a href="#how-we-share" class="blue-text hover:underline">3. How We Share Your Information</a></li>
          <li><a href="#data-security" class="blue-text hover:underline">4. Data Security</a></li>
          <li><a href="#your-rights" class="blue-text hover:underline">5. Your Data Protection Rights</a></li>
          <li><a href="#third-party" class="blue-text hover:underline">6. Third-Party Links</a></li>
          <li><a href="#children-privacy" class="blue-text hover:underline">7. Children's Privacy</a></li>
          <li><a href="#changes" class="blue-text hover:underline">8. Changes to This Policy</a></li>
          <li><a href="#contact" class="blue-text hover:underline">9. Contact Us</a></li>
        </ul>
      </div>

      <!-- Policy Content -->
      <div class="p-6 md:p-8 space-y-8">
        
        <!-- Introduction -->
        <div class="content-section">
          <p class="text-lg leading-relaxed">
            This Privacy Policy describes how <span class="font-semibold maroon-text"><a href="https://bhaktivedantagurukul.com/">https://bhaktivedantagurukul.com/</a></span> collects, uses, and shares your personal information when you use our website and related services.
          </p>
          <p class="mt-4 text-lg leading-relaxed">
            By using our Services, you agree to the terms of this Privacy Policy.
          </p>
        </div>

        <!-- 1. Information We Collect -->
        <div id="info-we-collect" class="content-section pt-4">
          <h2 class="text-2xl font-bold brown-text mb-4 flex items-center">
            <span class="bg-blue-100 blue-text rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
            Information We Collect
          </h2>
          <p>We collect information you provide directly to us and information about your use of our Services.</p>

          <div class="ml-8 space-y-6 mt-4">
            <div>
              <h3 class="text-xl font-semibold mb-2 brown-text">A. Information You Provide to Us:</h3>
              <ul class="list-disc pl-6 space-y-2">
                <li><span class="font-medium">Contact Information:</span> Such as your name and email address when you contact us, sign up for a newsletter, or create an account.</li>
                <li><span class="font-medium">Communication Data:</span> Records of your correspondence with us.</li>
                <li><span class="font-medium">User Content:</span> Any information you post or upload.</li>
              </ul>
            </div>

            <div>
              <h3 class="text-xl font-semibold mb-2 brown-text">B. Information We Collect Automatically:</h3>
              <ul class="list-disc pl-6 space-y-2">
                <li><span class="font-medium">Usage Data:</span> Information about your interaction with our site like IP, browser, pages visited, time/date, and links clicked.</li>
                <li><span class="font-medium">Cookies and Tracking:</span> We use cookies to enhance functionality. You can disable cookies via browser settings.</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- 2. How We Use -->
        <div id="how-we-use" class="content-section pt-4">
          <h2 class="text-2xl font-bold brown-text mb-4 flex items-center">
            <span class="bg-blue-100 blue-text rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
            How We Use Your Information
          </h2>
          <ul class="list-disc pl-6 space-y-2">
            <li>To operate and maintain our Services.</li>
            <li>To respond to inquiries and provide support.</li>
            <li>To send important updates and alerts.</li>
            <li>To monitor and improve user experience.</li>
            <li>To detect and prevent fraud or misuse.</li>
            <li>To comply with legal obligations.</li>
          </ul>
        </div>

        <!-- 3. How We Share -->
        <div id="how-we-share" class="content-section pt-4">
          <h2 class="text-2xl font-bold brown-text mb-4 flex items-center">
            <span class="bg-blue-100 blue-text rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
            How We Share Your Information
          </h2>
          <ul class="list-disc pl-6 space-y-2">
            <li><span class="font-medium">Service Providers:</span> Trusted vendors who help operate the site.</li>
            <li><span class="font-medium">Legal Requirements:</span> When required by law.</li>
            <li><span class="font-medium">Business Transfers:</span> In case of merger or sale.</li>
            <li><span class="font-medium">With Consent:</span> When you explicitly allow sharing.</li>
          </ul>
        </div>

        <!-- 4. Data Security -->
        <div id="data-security" class="content-section pt-4">
          <h2 class="text-2xl font-bold brown-text mb-4 flex items-center">
            <span class="bg-blue-100 blue-text rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
            Data Security
          </h2>
          <p>We implement appropriate security measures, but no method is 100% secure. You share information at your own risk.</p>
        </div>

        <!-- 5. Rights -->
        <div id="your-rights" class="content-section pt-4">
          <h2 class="text-2xl font-bold brown-text mb-4 flex items-center">
            <span class="bg-blue-100 blue-text rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
            Your Data Protection Rights
          </h2>
          <ul class="list-disc pl-6 space-y-2">
            <li><b>Access:</b> Request copies of your data.</li>
            <li><b>Rectification:</b> Correct inaccurate info.</li>
            <li><b>Erasure:</b> Ask for deletion where applicable.</li>
            <li><b>Restriction:</b> Limit how we process data.</li>
            <li><b>Portability:</b> Transfer data to another service.</li>
          </ul>
        </div>

        <!-- 6. Third Party -->
        <div id="third-party" class="content-section pt-4">
          <h2 class="text-2xl font-bold brown-text mb-4 flex items-center">
            <span class="bg-blue-100 blue-text rounded-full w-8 h-8 flex items-center justify-center mr-3">6</span>
            Third-Party Links
          </h2>
          <p>We are not responsible for external websites linked from our Services.</p>
        </div>

        <!-- 7. Children -->
        <div id="children-privacy" class="content-section pt-4">
          <h2 class="text-2xl font-bold brown-text mb-4 flex items-center">
            <span class="bg-blue-100 blue-text rounded-full w-8 h-8 flex items-center justify-center mr-3">7</span>
            Children's Privacy
          </h2>
          <p>We do not knowingly collect personal data from children under 13. Contact us if you believe a child has shared such data.</p>
        </div>

        <!-- 8. Changes -->
        <div id="changes" class="content-section pt-4">
          <h2 class="text-2xl font-bold brown-text mb-4 flex items-center">
            <span class="bg-blue-100 blue-text rounded-full w-8 h-8 flex items-center justify-center mr-3">8</span>
            Changes to This Policy
          </h2>
          <p>We may update this policy periodically and will post the revised version here.</p>
        </div>

        <!-- 9. Contact -->
        <div id="contact" class="content-section pt-4">
          <h2 class="text-2xl font-bold brown-text mb-4 flex items-center">
            <span class="bg-blue-100 blue-text rounded-full w-8 h-8 flex items-center justify-center mr-3">9</span>
            Contact Us
          </h2>
          <p>For questions, please reach us at:</p>
          <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="font-medium">Email: <span class="maroon-text">info@ourgurukul.org</span></p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Back to Top -->
  <button id="backToTop" class="back-to-top maroon-bg text-white p-3 rounded-full shadow-lg hover:bg-red-800 transition">
    <i class="fas fa-arrow-up"></i>
  </button>

  <!-- Footer -->
  <?php include 'includes/footer.php'; ?>

  <script>
    // Set Dates
    document.getElementById('lastUpdated').textContent = new Date().toLocaleDateString('en-US', {
      year: 'numeric', month: 'long', day: 'numeric'
    });

    // Back to Top
    const backToTopButton = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
      backToTopButton.style.display = window.pageYOffset > 300 ? 'block' : 'none';
    });
    backToTopButton.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetElement = document.querySelector(this.getAttribute('href'));
        if (targetElement) {
          window.scrollTo({ top: targetElement.offsetTop - 20, behavior: 'smooth' });
        }
      });
    });
  </script>
</body>
</html>
