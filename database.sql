CREATE DATABASE IF NOT EXISTS hostel_management;
USE hostel_management;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(30) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    dob DATE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    guardian_name VARCHAR(100) NOT NULL,
    guardian_contact VARCHAR(20) NOT NULL,
    admission_date DATE NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    room_type ENUM('Single','Double','Triple','Dormitory') NOT NULL,
    capacity INT NOT NULL,
    available_beds INT NOT NULL,
    floor_number INT NOT NULL,
    status ENUM('Available','Occupied','Maintenance') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE room_allocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    allocated_date DATE NOT NULL,
    status ENUM('Active','Vacated') DEFAULT 'Active',
    remarks TEXT,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_month VARCHAR(20) NOT NULL,
    payment_date DATE NOT NULL,
    mode ENUM('Cash','Card','UPI','Bank Transfer') NOT NULL,
    receipt_number VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('Paid','Due') DEFAULT 'Paid',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT DEFAULT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Open','In Progress','Resolved') DEFAULT 'Open',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
);

CREATE TABLE visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    visitor_name VARCHAR(100) NOT NULL,
    purpose VARCHAR(255) NOT NULL,
    entry_time DATETIME NOT NULL,
    exit_time DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    address TEXT NOT NULL,
    hired_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (full_name, username, password, email) VALUES
('System Administrator', 'admin', '$2y$12$y3bQv4ozlyf1uS6Eu0GnRefB5mkRXx/TmAYj.e2ZoALmJSZr3FfqS', 'admin@hostel.local');

INSERT INTO students (student_id, full_name, gender, dob, phone, email, address, guardian_name, guardian_contact, admission_date, photo) VALUES
('STU-1001', 'Aisha Khan', 'Female', '2002-04-10', '9876543210', 'aisha@example.com', 'Lahore, Pakistan', 'Mr. Khan', '9123456789', '2024-01-15', NULL),
('STU-1002', 'Rohit Sharma', 'Male', '2001-08-22', '9876543211', 'rohit@example.com', 'Delhi, India', 'Mrs. Sharma', '9123456788', '2023-11-10', NULL),
('STU-1003', 'Meera Patel', 'Female', '2003-02-14', '9876543212', 'meera@example.com', 'Ahmedabad, India', 'Mr. Patel', '9123456787', '2024-02-20', NULL);

INSERT INTO rooms (room_number, room_type, capacity, available_beds, floor_number, status) VALUES
('101', 'Single', 1, 1, 1, 'Available'),
('102', 'Double', 2, 1, 1, 'Available'),
('201', 'Double', 2, 0, 2, 'Occupied'),
('301', 'Triple', 3, 2, 3, 'Available');

INSERT INTO room_allocations (student_id, room_id, allocated_date, status, remarks) VALUES
(1, 3, '2024-02-01', 'Active', 'Regular student'),
(2, 1, '2024-02-05', 'Active', 'Allotted single room'),
(3, 4, '2024-03-01', 'Active', 'Room assigned for new term');

INSERT INTO payments (student_id, amount, payment_month, payment_date, mode, receipt_number, status) VALUES
(1, 8000.00, 'January 2025', '2025-01-05', 'UPI', 'RCPT-1001', 'Paid'),
(2, 8000.00, 'January 2025', '2025-01-10', 'Cash', 'RCPT-1002', 'Paid'),
(3, 8000.00, 'January 2025', '2025-01-12', 'Card', 'RCPT-1003', 'Paid');

INSERT INTO complaints (student_id, category, description, status) VALUES
(1, 'Water Supply', 'Water pressure is very low in the morning.', 'Open'),
(2, 'Cleaning', 'Washroom is not cleaned properly.', 'In Progress');

INSERT INTO visitors (student_id, visitor_name, purpose, entry_time, exit_time) VALUES
(1, 'Rahul Singh', 'Family Meeting', '2025-02-04 10:30:00', '2025-02-04 12:00:00'),
(2, 'Sonia Verma', 'Document Delivery', '2025-02-05 15:00:00', '2025-02-05 15:45:00');

INSERT INTO staff (full_name, role, phone, email, salary, address, hired_date) VALUES
('Neha Gupta', 'Warden', '9876543213', 'neha@hostel.local', 35000.00, 'College Campus', '2023-07-01'),
('Arun Kumar', 'Cleaner', '9876543214', 'arun@hostel.local', 18000.00, 'City Center', '2023-08-15');

SELECT 'Database initialized successfully.' AS status;
