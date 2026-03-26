CREATE DATABASE IF NOT EXISTS smart_college;
USE smart_college;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    user_id VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    role ENUM('Student', 'Admin') NOT NULL,
    security_question VARCHAR(255) DEFAULT NULL,
    security_answer VARCHAR(255) DEFAULT NULL,
    first_login INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a default admin for testing
INSERT INTO users (name, user_id, email, password, role) 
VALUES ('System Admin', 'admin01', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin'); 
-- Note: The default admin password is 'password'

CREATE TABLE IF NOT EXISTS valid_students (
    reg_no VARCHAR(50) PRIMARY KEY
);

-- Insert some dummy registration numbers for testing
INSERT INTO valid_students (reg_no) VALUES 
('STD1001'), 
('STD1002'), 
('STD1003'), 
('STD1004'), 
('STD1005');
