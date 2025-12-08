CREATE DATABASE IF NOT EXISTS school_admission;
USE school_admission;

CREATE TABLE admissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    alternate_phone VARCHAR(15),
    email VARCHAR(100),
    photo VARCHAR(255),
    signature VARCHAR(255),
    previous_school VARCHAR(255),
    class VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    admission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_status ENUM('pending', 'paid') DEFAULT 'pending',
    payment_id VARCHAR(100),
    amount DECIMAL(10,2) DEFAULT 500.00
);

CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admission_id INT,
    amount DECIMAL(10,2),
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20),
    FOREIGN KEY (admission_id) REFERENCES admissions(id)
);