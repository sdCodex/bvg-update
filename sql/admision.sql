-- Admissions Table
CREATE TABLE admissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    parent_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    grade_applying_for VARCHAR(20) NOT NULL,
    previous_school VARCHAR(100),
    birth_date DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    documents_submitted JSON,
    status ENUM('pending', 'under_review', 'approved', 'rejected') DEFAULT 'pending',
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admission Requirements Table
CREATE TABLE admission_requirements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    grade_level VARCHAR(20) NOT NULL,
    min_age_years INT NOT NULL,
    required_documents JSON NOT NULL,
    fee_amount DECIMAL(10,2) NOT NULL,
    academic_requirements TEXT,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admission Timeline Table
CREATE TABLE admission_timeline (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    description TEXT,
    academic_year VARCHAR(9) NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Fee Structure Table
CREATE TABLE fee_structure (
    id INT PRIMARY KEY AUTO_INCREMENT,
    grade_level VARCHAR(20) NOT NULL,
    tuition_fee DECIMAL(10,2) NOT NULL,
    admission_fee DECIMAL(10,2) NOT NULL,
    development_fee DECIMAL(10,2) NOT NULL,
    other_charges DECIMAL(10,2) DEFAULT 0,
    total_fee DECIMAL(10,2) AS (tuition_fee + admission_fee + development_fee + other_charges),
    academic_year VARCHAR(9) NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hostel Fees Table
CREATE TABLE hostel_fees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hostel_type VARCHAR(50) NOT NULL,
    room_charges DECIMAL(10,2) NOT NULL,
    food_charges DECIMAL(10,2) NOT NULL,
    other_charges DECIMAL(10,2) DEFAULT 0,
    total_fee DECIMAL(10,2) AS (room_charges + food_charges + other_charges),
    academic_year VARCHAR(9) NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);