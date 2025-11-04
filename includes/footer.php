<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bhaktivedanta Gurukul - Footer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .bg-primary {
            background-color: #1e3a5f;
        }

        .bg-secondary {
            background-color: #152642;
        }

        .text-accent {
            color: #f59e0b;
        }

        .border-accent {
            border-color: #f59e0b;
        }

        .map-container {
            height: 250px;
            border-radius: 8px;
            overflow: hidden;
        }

        .footer-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            padding-left: 8px;
        }

        .footer-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 2px;
            background-color: #f59e0b;
            transition: width 0.3s ease;
        }

        .footer-link:hover::before {
            width: 4px;
        }
    </style>
</head>

<body class="bg-gray-100">


    <!-- Footer -->
    <footer class="bg-primary text-white">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Main Footer Content -->
            <div class="pt-12 pb-8">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    <!-- School Info -->
                    <div class="lg:col-span-2">


                        <!-- Alternative Logo Section -->
                        <div class="lg:col-span-2">
                            <div class="flex items-center space-x-4 mb-4">

                                <!-- Agar white version logo nahi hai toh normal logo with background -->
                                <div class="bg-white rounded-lg p-2">
                                    <img src="<?php echo $base_url; ?>/images/BVG-Header.png" alt="Bhaktivedanta Gurukul Logo" class="h-12 w-auto">
                                </div>

                            </div>



                        </div>
                        <p class="text-gray-300 mb-6 leading-relaxed">
                            Combining modern education with traditional Vedic values for holistic development of students.
                            Our mission is to nurture young minds with knowledge, values, and skills for a meaningful life.
                        </p>
                        <div class="flex space-x-4">
                              <a href="https://whatsapp.com/channel/0029VbB2O3F8KMqcp0Eu4A21" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                             <a href="https://www.instagram.com/bhaktivedanta.gurukul" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.facebook.com/share/1Bd7wGt7PP/" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                <i class="fab fa-twitter"></i>
                            </a>
                           
                            <a href="https://www.youtube.com/@BhaktivedantaGurukul" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="https://www.linkedin.com/in/bhaktivedanta-gurukul/" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                          
                            <a href="https://t.me/BhaktivedantaGurukul" class="bg-secondary w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-white hover:bg-accent transition-all duration-300">
                                <i class="fab fa-telegram"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="font-serif text-lg font-semibold mb-6 pb-2 border-b border-accent inline-block">Quick Links</h4>
                        <ul class="space-y-3">
                            <li><a href="<?php echo $base_url; ?>/index.php" class="footer-link text-gray-300 hover:text-white block">Home</a></li>
                            <li><a href="<?php echo $base_url; ?>/about.php" class="footer-link text-gray-300 hover:text-white block">About Us</a></li>
                            <li><a href="<?php echo $base_url; ?>/pages/admissions/index.php" class="footer-link text-gray-300 hover:text-white block">Admissions</a></li>
                            <li><a href="<?php echo $base_url; ?>/programs/index.php" class="footer-link text-gray-300 hover:text-white block">Programs</a></li>
                            <li><a href="<?php echo $base_url; ?>/pages/scholarship/index.php" class="footer-link text-gray-300 hover:text-white block">Scholarship</a></li>
                            <li><a href="<?php echo $base_url; ?>/contact.php" class="footer-link text-gray-300 hover:text-white block">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div>
                        <h4 class="font-serif text-lg font-semibold mb-6 pb-2 border-b border-accent inline-block">Contact Info</h4>
                        <div class="space-y-4 text-gray-300">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-map-marker-alt text-accent mt-1"></i>
                                <span>Bhaktivedanta Gurukul, Near ISKCON Prayagraj,
                                    Mutthi Ganj, Prayagraj, UP- 211003</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-phone text-accent"></i>
                                <span>+91 7618040040</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-envelope text-accent"></i>
                                <span>info@ourgurukul.org</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-clock text-accent"></i>
                                <span>Mon - Sat: 8:00 AM - 2:00 PM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Map Section -->
                    <div>
                        <h4 class="font-serif text-lg font-semibold mb-6 pb-2 border-b border-accent inline-block">Our Location</h4>
                        <div class="map-container bg-secondary flex items-center justify-center">
                          
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3603.4065403354293!2d81.83626877605948!3d25.424668522448314!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39853530acb4005f%3A0x5e240b741f40b60a!2sISKCON%20Prayagraj%20-%20Shri%20Shri%20Radha%20Venimadhava%20Mandir!5e0!3m2!1sen!2sin!4v1761634042246!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-gray-600 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-center md:text-left mb-4 md:mb-0">
                        <p class="text-gray-300">&copy; 2025 Bhaktivedanta Gurukul School of Excellence. All rights reserved.</p>
                    </div>
                    <div class="flex space-x-6 text-sm">
                        <a href="" class="text-gray-300 hover:text-white transition-colors">Privacy Policy</a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">Terms of Service</a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">Sitemap</a>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <p class="text-sm text-gray-400">Designed with <i class="fas fa-heart text-red-500"></i> for quality education</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
</body>

</html>