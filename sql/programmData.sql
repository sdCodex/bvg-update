-- Insert Programs
INSERT INTO programs (title, description, duration, age_group, fees, display_order, featured) VALUES
('Primary Education', 'Our primary education program focuses on building strong foundations in core subjects while introducing students to Vedic values and cultural traditions in a nurturing environment.', '5 Years', '6-11 Years', '₹25,000/year', 1, TRUE),
('Middle School', 'The middle school program builds upon foundational knowledge while introducing specialized subjects and deeper spiritual understanding to prepare students for academic excellence.', '3 Years', '11-14 Years', '₹35,000/year', 2, TRUE),
('High School', 'Comprehensive high school education with advanced curriculum, career guidance, and intensive preparation for board examinations and competitive exams.', '2 Years', '14-16 Years', '₹45,000/year', 3, TRUE),
('Vedic Studies', 'Deep dive into ancient Vedic scriptures, philosophy, and traditions while maintaining academic excellence in modern subjects.', 'Ongoing', 'All Ages', 'Included in Main Program', 4, FALSE),
('Yoga & Meditation', 'Comprehensive yoga and meditation program focusing on physical health, mental wellness, and spiritual growth through ancient practices.', 'Ongoing', 'All Ages', 'Included in Main Program', 5, FALSE),
('IIT/JEE Preparation', 'Specialized coaching for IIT/JEE examinations with expert faculty, comprehensive study material, and regular mock tests.', '2 Years', '16-18 Years', '₹60,000/year', 6, FALSE),
('NEET Preparation', 'Focused NEET preparation program with medical experts, comprehensive biology coaching, and regular practice sessions.', '2 Years', '16-18 Years', '₹55,000/year', 7, FALSE);

-- Insert Program Features
INSERT INTO program_features (program_id, feature_text, icon, display_order) VALUES
(1, 'CBSE curriculum with integrated Vedic values and moral education', 'fas fa-book', 1),
(1, 'Comprehensive language development (English, Hindi, Sanskrit)', 'fas fa-language', 2),
(1, 'Interactive learning in mathematics and environmental science', 'fas fa-calculator', 3),
(1, 'Vedic stories, moral values, and cultural activities', 'fas fa-om', 4),
(1, 'Creative arts, music, yoga, and physical education', 'fas fa-paint-brush', 5),

(2, 'Advanced CBSE curriculum with project-based learning approach', 'fas fa-rocket', 1),
(2, 'In-depth study of Vedic philosophy and ancient scriptures', 'fas fa-graduation-cap', 2),
(2, 'State-of-the-art science and computer laboratories', 'fas fa-flask', 3),
(2, 'Leadership development and community service initiatives', 'fas fa-users', 4),
(2, 'Preparation for competitive exams and high school transition', 'fas fa-trophy', 5),

(3, 'Comprehensive CBSE curriculum with specialization options', 'fas fa-atom', 1),
(3, 'Career counseling and guidance for future studies', 'fas fa-briefcase', 2),
(3, 'Advanced laboratory facilities and research projects', 'fas fa-microscope', 3),
(3, 'Board examination preparation and mock tests', 'fas fa-clipboard-list', 4),
(3, 'Personality development and interview skills', 'fas fa-user-tie', 5);

-- Insert Curriculum
INSERT INTO program_curriculum (program_id, subject_category, subjects, description, display_order) VALUES
(1, 'Core Subjects', 'Mathematics, English, Hindi, Environmental Science', 'Fundamental subjects building strong academic foundation', 1),
(1, 'Vedic Studies', 'Moral Science, Vedic Stories, Sanskrit Shlokas', 'Introduction to Vedic values and cultural traditions', 2),
(1, 'Creative Arts', 'Drawing, Music, Dance, Craft', 'Development of creative expression and artistic skills', 3),
(1, 'Physical Education', 'Yoga, Sports, Games', 'Physical development and health awareness', 4),

(2, 'Core Subjects', 'Mathematics, Science, Social Studies, English, Hindi, Sanskrit', 'Comprehensive academic curriculum as per CBSE guidelines', 1),
(2, 'Vedic Philosophy', 'Bhagavad-gita, Vedic History, Spiritual Values', 'Deep understanding of Vedic philosophy and principles', 2),
(2, 'Practical Skills', 'Computer Science, Laboratory Work, Projects', 'Hands-on learning and practical application', 3),
(2, 'Co-curricular', 'Arts, Music, Sports, Debates', 'Holistic development through various activities', 4);

-- Insert Program Highlights
INSERT INTO program_highlights (program_id, highlight_key, highlight_value, description, display_order) VALUES
(1, 'Student-Teacher Ratio', '1:20', 'Personalized attention for each student', 1),
(1, 'Co-curricular Activities', '8+', 'Various activities for holistic development', 2),
(1, 'Language Options', '3', 'English, Hindi, and Sanskrit', 3),
(1, 'Value Based Education', '100%', 'Complete integration of moral values', 4),

(2, 'Student-Teacher Ratio', '1:25', 'Focused attention in advanced classes', 1),
(2, 'Laboratory Sessions', 'Weekly', 'Regular practical learning sessions', 2),
(2, 'Project Based Learning', 'Monthly', 'Hands-on project assignments', 3),
(2, 'Competition Preparation', 'Regular', 'Preparation for various competitions', 4);