-- Create database
CREATE DATABASE bhaktivedanta_gurukul;
USE bhaktivedanta_gurukul;

-- Contact messages table
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Scholarship submissions table
CREATE TABLE scholarship_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    parent_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    current_school VARCHAR(200),
    grade_applying VARCHAR(50),
    previous_marks VARCHAR(100),
    reason TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blog posts table
CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    author VARCHAR(100) NOT NULL,
    featured_image VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Programs table
CREATE TABLE programs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    duration VARCHAR(50),
    age_group VARCHAR(50),
    fees VARCHAR(100),
    image VARCHAR(200)
);

-- Testimonials table
CREATE TABLE testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    parent_name VARCHAR(100),
    testimonial TEXT NOT NULL,
    rating INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample programs
INSERT INTO programs (title, description, duration, age_group, fees) VALUES
('Primary Education (Grades 1-5)', 'Holistic development with focus on values, academics, and extracurricular activities', '5 Years', '6-11 Years', '₹35,000/year'),
('Middle School (Grades 6-8)', 'Comprehensive curriculum with Vedic studies and modern education', '3 Years', '11-14 Years', '₹45,000/year'),
('High School (Grades 9-10)', 'CBSE curriculum with spiritual values and character building', '2 Years', '14-16 Years', '₹55,000/year'),
('Vedic Studies Program', 'Traditional Vedic education alongside modern curriculum', '4 Years', '12-16 Years', '₹40,000/year'),
('Yoga & Meditation', 'Daily yoga, meditation, and spiritual practices', 'Ongoing', 'All Ages', 'Included in tuition'),
('Music & Arts', 'Traditional Indian music, dance, and fine arts', 'Ongoing', 'All Ages', '₹5,000/year');

-- Insert sample testimonials
INSERT INTO testimonials (student_name, parent_name, testimonial, rating) VALUES
('Rahul Sharma', 'Mr. & Mrs. Sharma', 'Bhaktivedanta Gurukul has transformed our son into a disciplined, knowledgeable, and compassionate individual. The blend of modern education with traditional values is exceptional.', 5),
('Priya Patel', 'Dr. Patel', 'The teachers are dedicated and the environment is nurturing. My daughter has excelled both academically and spiritually.', 5),
('Arjun Kumar', 'Mrs. Kumar', 'The focus on character building and moral values sets this school apart. Highly recommended for holistic development.', 5);

-- Insert sample blog posts
INSERT INTO blog_posts (title, excerpt, content, author, featured_image) VALUES
('Annual Sports Day 2024', 'Our school celebrated its annual sports day with great enthusiasm and participation from all students.', 'Full content about sports day events, winners, and celebrations...', 'Principal Sharma', 'sports-day.jpg'),
('Vedic Science Workshop', 'A special workshop on integrating Vedic knowledge with modern science was conducted for senior students.', 'Detailed content about the workshop sessions and learning outcomes...', 'Dr. Gupta', 'vedic-workshop.jpg'),
('New Yoga Program Launch', 'We are excited to announce our new daily yoga and meditation program for all students.', 'Information about the new yoga curriculum and benefits...', 'Yoga Teacher Das', 'yoga-program.jpg');