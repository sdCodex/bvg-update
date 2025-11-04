<?php
include '../../includes/db.php';


// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO scholarship_submissions 
            (student_name, parent_name, email, phone, address, current_school, grade_applying, previous_marks, reason) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $_POST['student_name'],
            $_POST['parent_name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['current_school'],
            $_POST['grade_applying'],
            $_POST['previous_marks'],
            $_POST['reason']
        ]);

        $success_message = "Thank you for your scholarship application! We will review it and contact you soon.";
    } catch (PDOException $e) {
        $error_message = "There was an error submitting your application. Please try again.";
    }
}
?>

<?php include '../../includes/header.php'; ?>


<link rel="stylesheet" href="../../css/scholorship.css">


<main class="font-sans bg-white">
    <!-- Header Include -->

    <!-- Hero Section -->
    <section class="bg-primary text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="font-serif text-4xl md:text-5xl font-bold mb-6">Scholarship Program</h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                Investing in deserving students for a brighter future through quality education
            </p>
        </div>
    </section>

    <!-- Scholarship Information -->
    <section class="section-padding bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-6">
                        Scholarship Opportunities
                    </h2>
                    <p class="text-lg text-secondary mb-6">
                        Bhaktivedanta Gurukul believes that financial constraints should not prevent
                        deserving students from receiving quality education with spiritual values.
                    </p>
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-award text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-primary mb-1">Merit-based Scholarships</h3>
                                <p class="text-secondary">For students with outstanding academic performance</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-heart text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-primary mb-1">Need-based Scholarships</h3>
                                <p class="text-secondary">For students from economically challenged backgrounds</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-music text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-primary mb-1">Talent Scholarships</h3>
                                <p class="text-secondary">For students excelling in arts, sports, or other talents</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-light rounded-2xl p-8">
                        <h3 class="font-serif text-2xl font-bold text-primary mb-6">Scholarship Benefits</h3>
                        <div class="space-y-4">
                            <div class="bg-white rounded-lg p-4">
                                <h4 class="font-semibold text-primary mb-2">Full Scholarship</h4>
                                <p class="text-secondary text-sm">100% tuition fee waiver + accommodation</p>
                                <div class="text-xs text-accent font-semibold mt-1">For exceptional candidates</div>
                            </div>
                            <div class="bg-white rounded-lg p-4">
                                <h4 class="font-semibold text-primary mb-2">Partial Scholarship</h4>
                                <p class="text-secondary text-sm">50-75% tuition fee waiver</p>
                                <div class="text-xs text-accent font-semibold mt-1">Based on merit and need</div>
                            </div>
                            <div class="bg-white rounded-lg p-4">
                                <h4 class="font-semibold text-primary mb-2">Special Talent Scholarship</h4>
                                <p class="text-secondary text-sm">25-50% fee waiver for specific talents</p>
                                <div class="text-xs text-accent font-semibold mt-1">Arts, sports, leadership</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Form -->
    <section class="section-padding bg-light">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">Scholarship Application</h2>
                <p class="text-xl text-secondary">
                    Complete the form below to apply for our scholarship program
                </p>
            </div>

            <?php if ($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Information -->
                        <div class="md:col-span-2">
                            <h3 class="font-serif text-xl font-bold text-primary mb-4 border-b pb-2">Student Information</h3>
                        </div>

                        <div>
                            <label for="student_name" class="block text-sm font-semibold text-primary mb-2">Student Full Name *</label>
                            <input type="text" id="student_name" name="student_name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>

                        <div>
                            <label for="grade_applying" class="block text-sm font-semibold text-primary mb-2">Grade Applying For *</label>
                            <select id="grade_applying" name="grade_applying" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                                <option value="">Select Grade</option>
                                <option value="Grade 1">Grade 1</option>
                                <option value="Grade 2">Grade 2</option>
                                <option value="Grade 3">Grade 3</option>
                                <option value="Grade 4">Grade 4</option>
                                <option value="Grade 5">Grade 5</option>
                                <option value="Grade 6">Grade 6</option>
                                <option value="Grade 7">Grade 7</option>
                                <option value="Grade 8">Grade 8</option>
 
                            </select>
                        </div>

                        <div>
                            <label for="current_school" class="block text-sm font-semibold text-primary mb-2">Current School</label>
                            <input type="text" id="current_school" name="current_school"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>

                        <div>
                            <label for="previous_marks" class="block text-sm font-semibold text-primary mb-2">Previous Year Marks/Percentage</label>
                            <input type="text" id="previous_marks" name="previous_marks"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent"
                                placeholder="e.g., 85% or A Grade">
                        </div>
                    </div>

                    <!-- Parent Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <h3 class="font-serif text-xl font-bold text-primary mb-4 border-b pb-2">Parent/Guardian Information</h3>
                        </div>

                        <div>
                            <label for="parent_name" class="block text-sm font-semibold text-primary mb-2">Parent/Guardian Name *</label>
                            <input type="text" id="parent_name" name="parent_name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-primary mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-primary mb-2">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div>
                        <label for="address" class="block text-sm font-semibold text-primary mb-2">Full Address *</label>
                        <textarea id="address" name="address" required rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent"
                            placeholder="Please provide your complete address"></textarea>
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-semibold text-primary mb-2">
                            Why are you applying for scholarship? *<br>
                            <span class="text-sm font-normal text-secondary">Please include information about your academic achievements, financial need, and why you want to join Bhaktivedanta Gurukul</span>
                        </label>
                        <textarea id="reason" name="reason" required rows="5"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent"
                            placeholder="Please provide detailed information..."></textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="agree" name="agree" required
                            class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                        <label for="agree" class="ml-2 text-sm text-secondary">
                            I certify that the information provided is true and accurate to the best of my knowledge
                        </label>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn-primary px-12">
                            Submit Application <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-8 text-secondary">
                <p>For any questions regarding the scholarship program, please contact our admissions office:</p>
                <p class="font-semibold text-primary mt-2">
                    <i class="fas fa-phone text-accent mr-2"></i>+91 5652 241234 |
                    <i class="fas fa-envelope text-accent mr-2 ml-4"></i>admissions@bhaktivedantagurukul.edu
                </p>
            </div>
        </div>
    </section>

    <!-- Scholarship Test Details -->
    <section class="section-padding bg-white">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-primary mb-4">Scholarship Test Details</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="text-center">
                    <div class="bg-light rounded-xl p-6">
                        <i class="fas fa-calendar-alt text-accent text-3xl mb-4"></i>
                        <h3 class="font-semibold text-primary mb-2">Test Dates</h3>
                        <p class="text-secondary text-sm">Quarterly tests conducted in March, June, September, and December</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="bg-light rounded-xl p-6">
                        <i class="fas fa-book text-accent text-3xl mb-4"></i>
                        <h3 class="font-semibold text-primary mb-2">Syllabus</h3>
                        <p class="text-secondary text-sm">Based on previous grade curriculum with focus on Mathematics, English, and General Knowledge</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="bg-light rounded-xl p-6">
                        <i class="fas fa-trophy text-accent text-3xl mb-4"></i>
                        <h3 class="font-semibold text-primary mb-2">Selection Process</h3>
                        <p class="text-secondary text-sm">Written test followed by personal interview for shortlisted candidates</p>
                    </div>
                </div>
            </div>

            <div class="bg-light rounded-2xl p-8">
                <h3 class="font-serif text-2xl font-bold text-primary mb-4">Important Notes</h3>
                <ul class="space-y-3 text-secondary">
                    <li class="flex items-start">
                        <i class="fas fa-info-circle text-accent mr-3 mt-1"></i>
                        <span>Scholarship applications are processed within 2-3 weeks of submission</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-info-circle text-accent mr-3 mt-1"></i>
                        <span>Shortlisted candidates will be contacted for a scholarship test and interview</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-info-circle text-accent mr-3 mt-1"></i>
                        <span>Scholarship renewal is subject to maintaining academic performance and good conduct</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-info-circle text-accent mr-3 mt-1"></i>
                        <span>Document verification will be conducted for all selected candidates</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Footer Include -->
    <?php include '../../includes/footer.php'; ?>
</main>