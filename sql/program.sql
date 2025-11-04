-- Programs Table
CREATE TABLE programs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    duration VARCHAR(50) NOT NULL,
    age_group VARCHAR(50) NOT NULL,
    fees VARCHAR(50) NOT NULL,
    display_order INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Program Features Table
CREATE TABLE program_features (
    id INT PRIMARY KEY AUTO_INCREMENT,
    program_id INT,
    feature_text TEXT NOT NULL,
    icon VARCHAR(50),
    display_order INT DEFAULT 0,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
);

-- Curriculum Table
CREATE TABLE program_curriculum (
    id INT PRIMARY KEY AUTO_INCREMENT,
    program_id INT,
    subject_category VARCHAR(100) NOT NULL,
    subjects TEXT NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
);

-- Program Highlights Table
CREATE TABLE program_highlights (
    id INT PRIMARY KEY AUTO_INCREMENT,
    program_id INT,
    highlight_key VARCHAR(100) NOT NULL,
    highlight_value VARCHAR(100) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
);