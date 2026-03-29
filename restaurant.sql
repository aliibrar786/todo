-- restaurant.sql
CREATE DATABASE IF NOT EXISTS restaurant_reservation;
USE restaurant_reservation;

-- Tables table
CREATE TABLE IF NOT EXISTS tables (
    table_id INT PRIMARY KEY AUTO_INCREMENT,
    table_no VARCHAR(10) NOT NULL,
    table_type VARCHAR(20) NOT NULL,
    capacity INT NOT NULL,
    status ENUM('available', 'occupied', 'reserved') DEFAULT 'available'
);

-- Reservations table
CREATE TABLE IF NOT EXISTS reservations (
    reservation_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    number_of_persons INT NOT NULL,
    table_no VARCHAR(10) NOT NULL,
    table_type VARCHAR(20) NOT NULL,
    occasion VARCHAR(50),
    special_request TEXT,
    advance_paid DECIMAL(10,2) DEFAULT 0,
    payment_status ENUM('pending', 'paid', 'partial') DEFAULT 'pending',
    booking_source VARCHAR(50),
    status ENUM('pending', 'confirmed', 'cancelled', 'seated') DEFAULT 'pending',
    cancelled_reason TEXT,
    notes TEXT,
    created_by VARCHAR(50)
);

-- Insert sample tables
INSERT INTO tables (table_no, table_type, capacity) VALUES
('T01', 'Window Side', 4),
('T02', 'Window Side', 4),
('T03', 'Family', 6),
('T04', 'Family', 6),
('T05', 'VIP', 8),
('T06', 'VIP', 8),
('T07', 'Regular', 2),
('T08', 'Regular', 2),
('T09', 'Outdoor', 4),
('T10', 'Outdoor', 4);

-- Insert sample reservations
INSERT INTO reservations (customer_name, phone, email, visit_date, visit_time, number_of_persons, table_no, table_type, occasion, payment_status, status) VALUES
('Ali Ahmed', '03001234567', 'ali@gmail.com', '2026-01-25', '19:00:00', 4, 'T01', 'Window Side', 'Dinner', 'paid', 'confirmed'),
('Sara Khan', '03111234567', 'sara@gmail.com', '2026-01-25', '20:00:00', 6, 'T03', 'Family', 'Birthday', 'partial', 'confirmed'),
('Usman Raza', '03211234567', 'usman@gmail.com', '2026-01-30', '13:00:00', 2, 'T08', 'Regular', 'Lunch', 'pending', 'pending'),
('Hassan Raza', '03201234567', 'hassan@gmail.com', '2026-01-24', '14:00:00', 3, 'T09', 'Regular', 'Lunch', 'pending', 'confirmed'),
('kareem Raza', '03211234598', 'kareem@gmail.com', '2026-01-22', '15:00:00', 4, 'T05', 'Regular', 'breakfast', 'pending', 'pending'),
('asadullah', '03211235670', 'asad@gmail.com', '2026-01-29', '16:00:00', 8, 'T06', 'Regular', 'Lunch', 'pending', 'confirmed'),
('hanif muhammad', '03221134567', 'hanif@gmail.com', '2026-01-28', '17:00:00', 10, 'T10', 'Regular', 'Lunch', 'pending', 'pending'),
('umer shahid', '03203214567', 'umer@gmail.com', '2026-01-26', '18:00:00', 9, 'T07', 'Regular', 'dinner', 'pending', 'confirmed');