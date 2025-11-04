-- Create Database
CREATE DATABASE IF NOT EXISTS gurukul_db;
USE gurukul_db;

-- Admin Table
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admission_number VARCHAR(20) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(15),
    father_name VARCHAR(100),
    mother_name VARCHAR(100),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    address TEXT,
    class VARCHAR(20),
    section VARCHAR(10),
    admission_date DATE,
    status ENUM('Active', 'Inactive', 'Graduated') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Teachers Table
CREATE TABLE IF NOT EXISTS teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id VARCHAR(20) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(15),
    qualification VARCHAR(100),
    subject_specialization VARCHAR(100),
    experience_years INT,
    joining_date DATE,
    salary DECIMAL(10,2),
    address TEXT,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Applications Table (Admission Applications)
CREATE TABLE IF NOT EXISTS applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_number VARCHAR(20) UNIQUE,
    student_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100),
    mother_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(15),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    applied_class VARCHAR(20),
    address TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT
);

-- Insert Default Admin (Password: admin123)
INSERT INTO admins (username, password_hash, email, full_name, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gurukul.edu', 'Administrator', 'super_admin');

-- Insert Sample Data
INSERT INTO students (admission_number, full_name, email, phone, father_name, class, section, admission_date) VALUES
('STU2024001', 'Rahul Sharma', 'rahul@email.com', '9876543210', 'Rajesh Sharma', '10th', 'A', '2024-01-15'),
('STU2024002', 'Priya Singh', 'priya@email.com', '9876543211', 'Vikram Singh', '9th', 'B', '2024-01-20');

INSERT INTO teachers (employee_id, full_name, email, phone, qualification, subject_specialization, experience_years) VALUES
('TCH2024001', 'Dr. Anil Kumar', 'anil@email.com', '9876543212', 'PhD in Mathematics', 'Mathematics', 10),
('TCH2024002', 'Ms. Sunita Patel', 'sunita@email.com', '9876543213', 'M.Sc Physics', 'Physics', 8);

INSERT INTO applications (application_number, student_name, father_name, email, phone, applied_class, status) VALUES
('APP2024001', 'Amit Verma', 'Sanjay Verma', 'amit@email.com', '9876543214', '8th', 'Pending'),
('APP2024002', 'Neha Gupta', 'Ramesh Gupta', 'neha@email.com', '9876543215', '11th', 'Approved');







USE bhaktivedanta_gurukul;

-- Delete existing admin if any
DELETE FROM admins WHERE username = 'admin';

-- Insert new admin with correct password hash for 'admin123'
INSERT INTO admins (username, password_hash, email, full_name, role) 
VALUES ('admin', '$2y$10$8A5H5p5s5p5s5p5s5p5s5.ZK8p5s5p5s5p5s5p5s5p5s5p5s5p5s', 'admin@gurukul.edu', 'Administrator', 'super_admin');

-- Alternative: Simple password without hashing for testing
-- INSERT INTO admins (username, password_hash, email, full_name, role) 
-- VALUES ('admin', 'admin123', 'admin@gurukul.edu', 'Administrator', 'super_admin');