CREATE DATABASE IF NOT EXISTS home_services;
USE home_services;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'worker', 'admin') NOT NULL DEFAULT 'customer',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE worker_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    service_id INT,
    experience INT DEFAULT 0,
    availability ENUM('available', 'busy', 'unavailable')
        DEFAULT 'available',
    earnings DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    worker_id INT NULL,
    service_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    address TEXT NOT NULL,
    notes TEXT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM(
        'Pending',
        'Accepted',
        'Rejected',
        'In Progress',
        'Completed',
        'Cancelled'
    ) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (worker_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'Cash',
    payment_status ENUM('Pending', 'Paid', 'Failed')
        DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

INSERT INTO services (service_name, description, price) VALUES
('Plumbing', 'Tap repair, pipe repair and water leakage services', 300.00),
('Electrical', 'Electrical wiring, fan and light repairs', 400.00),
('House Cleaning', 'Home cleaning and housekeeping services', 500.00),
('AC Repair', 'Air conditioner servicing and repair', 600.00),
('Painting', 'Interior and exterior painting services', 1000.00),
('Carpentry', 'Furniture repair and carpentry services', 350.00);