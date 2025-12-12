-- ResQ Database Schema
-- Created for ResQ B.V. inventory and reservation management system

-- Drop existing tables if they exist (for fresh install)
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS locations;
DROP TABLE IF EXISTS warehouses;
DROP TABLE IF EXISTS users;

-- Users table (employees and admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('employee', 'admin') DEFAULT 'employee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Warehouses table
CREATE TABLE warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    layout_json TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Locations table (cabinets, shelves within warehouses)
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warehouse_id INT NOT NULL,
    cabinet VARCHAR(50) NOT NULL,
    shelf VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    INDEX idx_warehouse (warehouse_id),
    INDEX idx_cabinet (cabinet)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Items table (inventory items)
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    description TEXT,
    status ENUM('available', 'reserved', 'picked_up', 'returned', 'defective', 'maintenance') DEFAULT 'available',
    qr_code VARCHAR(255),
    qr_code_path VARCHAR(500),
    location_id INT,
    warehouse_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_location (location_id),
    INDEX idx_warehouse (warehouse_id),
    INDEX idx_qr_code (qr_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reservations table
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    reservation_date DATETIME NOT NULL,
    pickup_date DATETIME,
    return_date DATETIME,
    status ENUM('pending', 'confirmed', 'picked_up', 'returned', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_item (item_id),
    INDEX idx_status (status),
    INDEX idx_reservation_date (reservation_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123 - CHANGE IN PRODUCTION!)
-- Password hash for 'admin123'
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@resq.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert default warehouse
INSERT INTO warehouses (name, description) VALUES
('Hoofdmagazijn', 'Hoofd magazijn locatie');

-- Insert sample locations
INSERT INTO locations (warehouse_id, cabinet, shelf, description) VALUES
(1, 'A1', 'Plank 1', 'Kast A1, eerste plank'),
(1, 'A1', 'Plank 2', 'Kast A1, tweede plank'),
(1, 'A2', 'Plank 1', 'Kast A2, eerste plank'),
(1, 'A3', 'Plank 1', 'Kast A3, eerste plank'),
(1, 'B1', 'Plank 1', 'Kast B1, eerste plank');

