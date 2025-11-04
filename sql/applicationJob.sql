CREATE TABLE job_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    position_type VARCHAR(50) NOT NULL,
    position_applied VARCHAR(100) NOT NULL,
    experience_years DECIMAL(3,1),
    current_organization VARCHAR(255),
    current_position VARCHAR(255),
    expected_salary VARCHAR(100),
    resume_path VARCHAR(500),
    cover_letter_path VARCHAR(500),
    additional_info TEXT,
    status ENUM('pending', 'reviewed', 'shortlisted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);