-- Create database
CREATE DATABASE IF NOT EXISTS shangrila_db;
USE shangrila_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    room_type VARCHAR(50) NOT NULL,
    floor_number INT NOT NULL,
    capacity INT NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    description TEXT,
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create room facilities table
CREATE TABLE IF NOT EXISTS room_facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create room_facility_mapping table
CREATE TABLE IF NOT EXISTS room_facility_mapping (
    room_id INT,
    facility_id INT,
    PRIMARY KEY (room_id, facility_id),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES room_facilities(id) ON DELETE CASCADE
);

-- Create reservations table
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    user_id INT,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    guest_email VARCHAR(100) NOT NULL,
    guest_phone VARCHAR(20) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seen_by_admin TINYINT(1) DEFAULT 0,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@shangrila.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample regular user (password: user123)
INSERT INTO users (name, email, password, role) VALUES 
('Regular User', 'user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert sample room facilities
INSERT INTO room_facilities (name, description, icon) VALUES
('Wi-Fi', 'High-speed wireless internet access', 'fas fa-wifi'),
('Air Conditioning', 'Individual climate control', 'fas fa-snowflake'),
('Mini Bar', 'Stocked with beverages and snacks', 'fas fa-glass-martini-alt'),
('Safe', 'In-room electronic safe', 'fas fa-lock'),
('TV', '55-inch Smart TV with cable channels', 'fas fa-tv'),
('Coffee Maker', 'In-room coffee and tea facilities', 'fas fa-coffee'),
('Balcony', 'Private balcony with view', 'fas fa-door-open'),
('Bathtub', 'Luxury bathtub with shower', 'fas fa-bath');

-- Insert sample rooms
INSERT INTO rooms (room_number, room_type, floor_number, capacity, price_per_night, description) VALUES
('101', 'Deluxe Lake View', 1, 2, 227.00, 'Spacious room with stunning views of Beira Lake'),
('102', 'Deluxe Lake View', 1, 2, 227.00, 'Spacious room with stunning views of Beira Lake'),
('201', 'Premier Ocean View', 2, 2, 350.00, 'Luxurious room with panoramic ocean views'),
('202', 'Premier Ocean View', 2, 2, 350.00, 'Luxurious room with panoramic ocean views'),
('301', 'Executive Suite', 3, 3, 500.00, 'Elegant suite with separate living area');

-- Map facilities to rooms
INSERT INTO room_facility_mapping (room_id, facility_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7), (2, 8),
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6), (3, 7), (3, 8),
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 6), (4, 7), (4, 8),
(5, 1), (5, 2), (5, 3), (5, 4), (5, 5), (5, 6), (5, 7), (5, 8); 