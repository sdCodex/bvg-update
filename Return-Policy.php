<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Policy - Bhaktivedanta Gurukul School of Excellence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="images/bvgLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ===== Custom Theme Colors ===== */
        .maroon-bg {
            background-color: #800000;
        }

        .maroon-text {
            color: #800000;
        }

        .brown-text {
            color: #3e2723;
        }

        .blue-text {
            color: #003366;
        }

        .maroon-border {
            border-color: #800000;
        }

        /* ===== Transitions & Effects ===== */
        .content-section {
            transition: all 0.3s ease;
        }

        .content-section:hover {
            transform: translateX(5px);
        }

        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
        }

        .timeline-item {
            border-left: 3px solid #800000;
            padding-left: 1.5rem;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 0;
            width: 1rem;
            height: 1rem;
            background: #800000;
            border-radius: 50%;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <?php include 'includes/header.php'; ?>

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 maroon-bg rounded-lg flex items-center justify-center">
                        <i class="fas fa-hand-holding-usd text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold maroon-text">Refund Policy</h1>
                </div>
                <div class="text-sm text-gray-500">
                    Last Updated: <span id="lastUpdated">8 Nov 2025</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">

            <!-- Important Notice -->
            <!--<div class="bg-yellow-50 border-l-4 border-yellow-400 p-6">-->
            <!--    <div class="flex items-start">-->
            <!--        <i class="fas fa-exclamation-triangle text-yellow-400 text-xl mr-3"></i>-->
                 
            <!--    </div>-->
            <!--</div>-->

            <!-- Table of Contents -->
            <div class="bg-gray-100 p-6 border-b maroon-border border-l-4">
                <h2 class="text-lg font-semibold mb-3 maroon-text">Table of Contents</h2>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <li><a href="#general-policy" class="maroon-text hover:underline">1. General Refund Policy</a></li>
                   

                    <li><a href="#process" class="maroon-text hover:underline">2. Refund Process</a></li>
                 
                    <li><a href="#contact" class="maroon-text hover:underline">3. Contact Us</a></li>
                </ul>
            </div>

            <!-- Policy Content -->
            <div class="p-6 md:p-8 space-y-8">

                <!-- 1. General Refund Policy -->
                <section id="general-policy" class="content-section">
                    <h2 class="text-2xl font-bold maroon-text mb-4 flex items-center">
                        <span class="bg-maroon-100 text-white maroon-bg rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                        General Refund Policy
                    </h2>
                    <p class="mb-4">
                        At <span class="font-semibold maroon-text">Bhaktivedanta Gurukul School of Excellence</span>, 
                        we strive to ensure customer satisfaction with our services. 
                        Our refund policy is designed to be fair and transparent.
                    </p>

                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 maroon-border">
                        <h3 class="font-semibold text-lg mb-2 brown-text">Key Points:</h3>
                        <ul class="list-disc pl-6 space-y-2 text-sm">
                            <li>Refund requests must be submitted within <strong>7 days</strong> of purchase</li>
                            <li>Approved refunds will be processed to the original payment method</li>
                            <li>Processing fees may be deducted from the refund amount</li>
                            <li>Digital products may have specific conditions</li>
                        </ul>
                    </div>
                </section>

                <!-- 2. Refund Process -->
                <section id="process" class="content-section">
                    <h2 class="text-2xl font-bold maroon-text mb-4 flex items-center">
                        <span class="bg-maroon-100 text-white maroon-bg rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                        Refund Process
                    </h2>

                    <div class="space-y-6 mt-4">
                        <div class="timeline-item">
                            <h3 class="font-semibold text-lg brown-text">Step 1: Submit Request</h3>
                            <p class="text-gray-600">
                                Contact our support team at 
                                <span class="font-semibold blue-text">info@ourgurukul.org</span> 
                                with your order details and refund reason.
                            </p>
                        </div>

                        <div class="timeline-item">
                            <h3 class="font-semibold text-lg brown-text">Step 2: Documentation</h3>
                            <p class="text-gray-600">
                                Provide supporting documents such as screenshots or images for digital/physical issues.
                            </p>
                        </div>

                        <div class="timeline-item">
                            <h3 class="font-semibold text-lg brown-text">Step 3: Review</h3>
                            <p class="text-gray-600">
                                Our team will review your request within <strong>7 business days</strong>.
                            </p>
                        </div>

                        <div class="timeline-item">
                            <h3 class="font-semibold text-lg brown-text">Step 4: Approval & Processing</h3>
                             <p class="text-gray-600">
                                If approved, refund will be processed and credited to your original payment method within 10 days.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- 3. Contact Us -->
                <section id="contact" class="content-section">
                    <h2 class="text-2xl font-bold maroon-text mb-4 flex items-center">
                        <span class="bg-maroon-100 text-white maroon-bg rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                        Contact Us
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6 mt-4">
                        <div class="bg-maroon-50 p-4 rounded-lg border maroon-border">
                            <h3 class="font-semibold maroon-text mb-3">Refund Support</h3>
                            <ul class="space-y-2 text-sm">
                                <li><i class="fas fa-envelope text-maroon-600 mr-2"></i>Email: <span class="blue-text">info@ourgurukul.org</span></li>
                                <li><i class="fas fa-phone text-maroon-600 mr-2"></i>Phone: <span class="blue-text">+91 7618040040</span></li>
                                <li><i class="fas fa-clock text-maroon-600 mr-2"></i>Response Time: <span class="blue-text">24–48 hours</span></li>
                            </ul>
                        </div>

                        <div class="bg-gray-100 p-4 rounded-lg border-l-4 maroon-border">
                            <h3 class="font-semibold blue-text mb-3">Information Required</h3>
                            <ul class="list-disc pl-4 text-sm space-y-1">
                                <li>Order number/Transaction ID</li>
                                <!--<li>Product/Service name</li>-->
                                <!--<li>Purchase date</li>-->
                                <li>Reason for refund request</li>
                                <li>Supporting evidence</li>
                            </ul>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </main>

    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top maroon-bg text-white p-3 rounded-full shadow-lg hover:bg-red-800 transition">
        <i class="fas fa-arrow-up"></i>
    </button>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Update date
        document.getElementById('lastUpdated').textContent = new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Back-to-top visibility
        const btn = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            btn.style.display = window.pageYOffset > 300 ? 'block' : 'none';
        });
        btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    </script>
</body>
</html>
