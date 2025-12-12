<?php
$base_url = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') ? '/Gurukul_website' : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scholarship & Admission Options | Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="./images/bvgLogo.png" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#800000',
                        secondary: '#3e2723',
                        accent: '#003366',
                        'accent-light': '#004080',
                    },
                    screens: {
                        'xs': '475px',
                    }
                },
            },
        }
    </script>

    <style>
        /* Custom styles for better responsiveness */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 640px) {
            .card-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        .feature-card {
            transition: all 0.3s ease;
            height: fit-content;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .stat-item {
            flex: 1 1 0px;
            min-width: 120px;
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include './includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-accent text-white py-12 md:py-16 lg:py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-semibold mb-6 md:mb-8">
                    <i class="fas fa-star text-yellow-300"></i>
                    <span>Limited Seats Available for 2026-27</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-3xl xs:text-4xl sm:text-5xl font-bold leading-tight mb-4 md:mb-6">
                    Scholarship & Admission
                    <span class="block text-yellow-300 mt-2">Opportunities</span>
                </h1>

                <!-- Description -->
                <p class="text-lg sm:text-xl text-blue-100 max-w-2xl mx-auto mb-6 md:mb-8 leading-relaxed">
                    Let your child have Acadmemic excellencia with Vedic roots
                </p>

                <!-- Stats -->
                <div class="flex flex-wrap justify-center gap-4 md:gap-6 mb-8">
                    <div class="stat-item bg-white/20 rounded-lg px-3 py-2 text-center">
                        <div class="text-xl font-bold">IITians</div>
                        <div class="text-sm opacity-90">initiative</div>
                    </div>
                    <div class="stat-item bg-white/20 rounded-lg px-3 py-2 text-center">
                        <div class="text-xl font-bold">50+</div>
                        <div class="text-sm opacity-90">Students</div>
                    </div>
                    <div class="stat-item bg-white/20 rounded-lg px-3 py-2 text-center">
                        <div class="text-xl font-bold">95%</div>
                        <div class="text-sm opacity-90">Success Rate</div>
                    </div>

                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col xs:flex-row gap-3 justify-center">
                    <a href="#programs" class="inline-flex items-center justify-center gap-2 bg-white text-accent font-bold px-6 py-3 rounded-lg hover:bg-blue-50 transition-all duration-300">
                        Explore Programs
                        <i class="fas fa-arrow-down ml-1"></i>
                    </a>

                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">

        <!-- Programs Section -->
        <section id="programs" class="mb-16 md:mb-20 lg:mb-24">
            <div class="text-center mb-10 md:mb-12 lg:mb-16">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-accent mb-4">
                    Choose Your Educational Path
                </h2>

            </div>

            <!-- Cards Container -->
            <div class="card-container">

                <!-- Card 1: Fortunate 51 -->
                <div class="feature-card bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-accent to-blue-900 text-white p-5 sm:p-6 relative">
                        <div class="absolute top-4 right-4 bg-yellow-100 text-accent px-3 py-1 rounded-full text-xs font-bold">
                            <i class="fas fa-crown mr-1"></i>Most Popular
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold mb-2">Fortunate 51</h3>
                        <p class="text-blue-200 text-sm sm:text-base">100% Scholarship with Residential Facilities</p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 sm:p-6">
                        <!-- Features -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-start gap-4">
                                <div class="bg-blue-100 p-2 rounded-lg flex-shrink-0 mt-1">
                                    <i class="fas fa-graduation-cap text-accent text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-base">Full Scholarship</h4>
                                    <p class="text-gray-600 text-sm">Complete tuition fee waiver for selected students</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="bg-blue-100 p-2 rounded-lg flex-shrink-0 mt-1">
                                    <i class="fas fa-home text-accent text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-base">Residential Program</h4>
                                    <p class="text-gray-600 text-sm">Safe hostel accommodation with meals</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="bg-blue-100 p-2 rounded-lg flex-shrink-0 mt-1">
                                    <i class="fas fa-award text-accent text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-base">Merit-Based</h4>
                                    <p class="text-gray-600 text-sm">Entrance test and interview selection</p>
                                </div>
                            </div>
                        </div>

                        <!-- Program Details -->
                        <div class="bg-blue-50 rounded-lg p-4 mb-6">
                            <h5 class="font-semibold text-accent text-sm mb-2">PROGRAM DETAILS:</h5>
                            <ul class="text-xs text-gray-700 space-y-1">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>Classes 6-8 (Only boys as per current policy)</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>Max 51 deserving candidates of each class</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>Complete Residential Program</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>Paid meals with free accommodation </span>
                                </li>
                            </ul>
                        </div>

                        <!-- Action Button -->
                        <a href="<?php echo $base_url; ?>/admissions/forms/fortunate.php"
                            class="block w-full bg-accent hover:bg-blue-800 text-white text-center font-semibold py-3 rounded-lg transition duration-300 text-sm sm:text-base">
                            Apply for Fortunate 51
                        </a>
                    </div>
                </div>

                <!-- Card 2: Prayagraj Admission -->
                <div class="feature-card bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-primary to-red-800 text-white p-5 sm:p-6">
                        <h3 class="text-xl sm:text-2xl font-bold mb-2">Prayagraj Center</h3>
                        <p class="text-red-200 text-sm sm:text-base">Holistic Education with Modern Facilities</p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 sm:p-6">
                        <!-- Features -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-start gap-4">
                                <div class="bg-red-100 p-2 rounded-lg flex-shrink-0 mt-1">
                                    <i class="fas fa-university text-primary text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-base">Prime Location</h4>
                                    <p class="text-gray-600 text-sm">Heart of educational hub in Prayagraj</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="bg-red-100 p-2 rounded-lg flex-shrink-0 mt-1">
                                    <i class="fas fa-users text-primary text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-base">Expert Faculty</h4>
                                    <p class="text-gray-600 text-sm">Experienced teachers and mentors</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="bg-red-100 p-2 rounded-lg flex-shrink-0 mt-1">
                                    <i class="fas fa-flask text-primary text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-base">Modern Labs</h4>
                                    <p class="text-gray-600 text-sm">State-of-the-art facilities</p>
                                </div>
                            </div>
                        </div>

                        <!-- Program Details -->
                        <div class="bg-red-50 rounded-lg p-4 mb-6">
                            <h5 class="font-semibold text-primary text-sm mb-2">PROGRAM HIGHLIGHTS:</h5>
                            <ul class="text-xs text-gray-700 space-y-1">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>Day Scholar Program</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>CBSE Curriculum</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>Value-Based Education</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <a href="<?php echo $base_url; ?>/pages/admissions/prayagraj/index.php"
                            class="block w-full bg-primary hover:bg-red-800 text-white text-center font-semibold py-3 rounded-lg transition duration-300 mb-3 text-sm sm:text-base">
                            Apply for Prayagraj
                        </a>
                        <a href="<?php echo $base_url; ?>/download.php">
                            <button class="w-full border border-primary text-primary hover:bg-red-50 font-semibold py-3 rounded-lg transition duration-300 text-sm sm:text-base">
                                Download Brochure
                            </button>
                        </a>

                    </div>
                </div>

                <!-- Card 3: Khargone Admission -->
                <div class="feature-card bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-secondary to-amber-900 text-white p-5 sm:p-6">
                        <h3 class="text-xl sm:text-2xl font-bold mb-2">Khargone Center</h3>
                        <p class="text-amber-200 text-sm sm:text-base">Quality Education in Indore Division</p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 sm:p-6">
                        <!-- Features -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-start gap-4">
                                <div class="bg-amber-100 p-2 rounded-lg flex-shrink-0 mt-1">
                                    <i class="fas fa-map-marker-alt text-secondary text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-base">Strategic Location</h4>
                                    <p class="text-gray-600 text-sm">Easy access from Indore division</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="bg-amber-100 p-2 rounded-lg flex-shrink-0 mt-1">
                                    <i class="fas fa-book text-secondary text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-base">Comprehensive Curriculum</h4>
                                    <p class="text-gray-600 text-sm">Well-structured academic programs</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="bg-amber-100 p-2 rounded-lg flex-shrink-0 mt-1">
                                    <i class="fas fa-chart-line text-secondary text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-base">Proven Results</h4>
                                    <p class="text-gray-600 text-sm">Excellent academic track record</p>
                                </div>
                            </div>
                        </div>

                        <!-- Program Details -->
                        <div class="bg-amber-50 rounded-lg p-4 mb-6">
                            <h5 class="font-semibold text-secondary text-sm mb-2">SPECIAL FEATURES:</h5>
                            <ul class="text-xs text-gray-700 space-y-1">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>Regional Focus</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>Affordable Fees</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                                    <span>Strong Alumni Network</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <a href="<?php echo $base_url; ?>/pages/admissions/khargone/khargone.php"
                            class="block w-full bg-secondary hover:bg-amber-900 text-white text-center font-semibold py-3 rounded-lg transition duration-300 mb-3 text-sm sm:text-base">
                            Apply for Khargone
                        </a>
                        <button class="w-full border border-secondary text-secondary hover:bg-amber-50 font-semibold py-3 rounded-lg transition duration-300 text-sm sm:text-base">
                            Talk to Counsellor
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="bg-white rounded-xl shadow-lg p-6 sm:p-8 md:p-10 mb-12 md:mb-16">
            <div class="text-center mb-8 md:mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-accent mb-4">
                    Why Choose Bhaktivedanta Gurukul?
                </h2>
                <p class="text-gray-600 max-w-3xl mx-auto text-base sm:text-lg">
                    We provide holistic education combining academic excellence with character building and spiritual values
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                <div class="text-center p-4 sm:p-6">
                    <div class="bg-blue-100 w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center mx-auto mb-4 transition-transform duration-300 hover:scale-110">
                        <i class="fas fa-trophy text-accent text-2xl sm:text-3xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-accent mb-3">Proven Excellence</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Outstanding academic results with consistent board performance and Olympiad achievements
                    </p>
                </div>

                <div class="text-center p-4 sm:p-6">
                    <div class="bg-red-100 w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center mx-auto mb-4 transition-transform duration-300 hover:scale-110">
                        <i class="fas fa-user-graduate text-primary text-2xl sm:text-3xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-primary mb-3">Student-Centric</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Personalized attention with small batches, mentoring, and regular doubt-solving sessions
                    </p>
                </div>

                <div class="text-center p-4 sm:p-6">
                    <div class="bg-amber-100 w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center mx-auto mb-4 transition-transform duration-300 hover:scale-110">
                        <i class="fas fa-hand-holding-heart text-secondary text-2xl sm:text-3xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-secondary mb-3">Value-Based</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Daily spiritual practices and cultural activities that build discipline and strong character
                    </p>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="contact" class="bg-white rounded-xl shadow-lg p-6 sm:p-8 md:p-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-accent mb-8 md:mb-12">
                Admission Process & Support
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 rounded-xl p-5 sm:p-6">
                    <h3 class="font-semibold text-lg flex items-center gap-3 text-accent mb-4">
                        <i class="fas fa-list-check"></i> Eligibility Criteria
                    </h3>
                    <ul class="text-gray-700 space-y-2 text-sm sm:text-base">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Class-wise percentage requirements</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Entrance test performance</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Value alignment with Gurukul culture</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-gray-50 rounded-xl p-5 sm:p-6">
                    <h3 class="font-semibold text-lg flex items-center gap-3 text-primary mb-4">
                        <i class="fas fa-route"></i> Admission Process
                    </h3>
                    <ul class="text-gray-700 space-y-2 text-sm sm:text-base">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Online application submission</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Entrance test/interview</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Document verification</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-gray-50 rounded-xl p-5 sm:p-6">
                    <h3 class="font-semibold text-lg flex items-center gap-3 text-secondary mb-4">
                        <i class="fas fa-headset"></i> Need Help?
                    </h3>
                    <ul class="text-gray-700 space-y-2 text-sm sm:text-base">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Call admission helpline</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Visit campus for tour</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Talk to counsellor</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <div class="border-t border-gray-200 mt-12"></div>

    <?php include './includes/footer.php'; ?>
</body>

</html>