-- BAA ESTATES AGENCY - Database Schema
-- Import via phpMyAdmin or: mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS baa_estates
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE baa_estates;

-- ============================================================
-- USERS (admins)
-- ============================================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','editor') NOT NULL DEFAULT 'admin',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin: admin@baaestates.com / Admin@123
-- IMPORTANT: change this password after first login.
INSERT INTO users (fullname, email, password_hash, role) VALUES
('BAA Admin', 'admin@baaestates.com',
 '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlWQZ1bMZ9yCqK8qN9fQY4e1b5eK2u', 'admin');

-- ============================================================
-- LOCATIONS
-- ============================================================
CREATE TABLE locations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    state VARCHAR(80) DEFAULT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO locations (name, slug, state, description) VALUES
('Lekki Phase 1', 'lekki-phase-1', 'Lagos', 'Prime residential area in Lagos with modern estates and easy access to Victoria Island.'),
('Ikeja GRA', 'ikeja-gra', 'Lagos', 'Government Reserved Area known for serenity, tree-lined streets, and proximity to the airport.'),
('Abuja City Centre', 'abuja-city-centre', 'FCT', 'Central business district with premium apartments, offices, and government buildings.'),
('Port Harcourt GRA', 'port-harcourt-gra', 'Rivers', 'Old GRA with established neighborhoods, close to major businesses and schools.');

-- ============================================================
-- PROPERTIES
-- ============================================================
CREATE TABLE properties (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    property_type ENUM('house','land') NOT NULL,
    listing_type ENUM('sale','lease') NOT NULL,
    status ENUM('available','sold','leased','pending') NOT NULL DEFAULT 'available',
    price DECIMAL(15,2) NOT NULL,
    price_period ENUM('total','month','year') DEFAULT NULL,
    bedrooms INT UNSIGNED DEFAULT NULL,
    bathrooms INT UNSIGNED DEFAULT NULL,
    area_sqft INT UNSIGNED DEFAULT NULL,
    location_id INT UNSIGNED DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    latitude DECIMAL(10,7) DEFAULT NULL,
    longitude DECIMAL(10,7) DEFAULT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (property_type),
    INDEX idx_listing (listing_type),
    INDEX idx_status (status),
    INDEX idx_location (location_id),
    INDEX idx_featured (featured),
    CONSTRAINT fk_property_location FOREIGN KEY (location_id)
        REFERENCES locations(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- PROPERTY IMAGES
-- ============================================================
CREATE TABLE property_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    property_id INT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_property (property_id),
    CONSTRAINT fk_image_property FOREIGN KEY (property_id)
        REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- INQUIRIES (contact form submissions)
-- ============================================================
CREATE TABLE inquiries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    property_id INT UNSIGNED DEFAULT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) DEFAULT NULL,
    message TEXT NOT NULL,
    status ENUM('new','read','replied') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_property (property_id),
    CONSTRAINT fk_inquiry_property FOREIGN KEY (property_id)
        REFERENCES properties(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- SEED PROPERTIES (sample data so homepage isn't empty)
-- ============================================================
INSERT INTO properties
(title, slug, description, property_type, listing_type, status, price, price_period, bedrooms, bathrooms, area_sqft, location_id, address, featured)
VALUES
('Modern 4-Bedroom Duplex in Lekki', 'modern-4-bedroom-duplex-lekki',
 'A stunning contemporary duplex featuring 4 spacious bedrooms, all en-suite, a fitted kitchen, generous living areas, and a private compound with space for 3 cars. Located in a gated estate with 24/7 security.',
 'house', 'sale', 'available', 85000000.00, 'total', 4, 5, 3200, 1, 'Plot 12, Admiralty Way, Lekki Phase 1', 1),

('Serviced 2-Bedroom Apartment in Ikeja GRA', 'serviced-2-bedroom-apartment-ikeja',
 'Fully serviced apartment with 2 en-suite bedrooms, modern kitchen, standby generator, treated water, and access to a shared gym and pool. Ideal for young professionals.',
 'house', 'lease', 'available', 3500000.00, 'year', 2, 3, 1200, 2, '15 Oduduwa Crescent, Ikeja GRA', 1),

('Prime Commercial Land in Abuja City Centre', 'prime-commercial-land-abuja',
 'Half-plot of prime commercial land suitable for office development. Fully documented with Certificate of Occupancy. Excellent road frontage and utilities available.',
 'land', 'sale', 'available', 120000000.00, 'total', NULL, NULL, 5400, 3, 'Central Business District, Abuja', 1),

('Residential Land in Port Harcourt GRA', 'residential-land-port-harcourt',
 'A well-positioned residential plot in the heart of Old GRA. Perfect for building your dream home. Quiet neighborhood with good drainage.',
 'land', 'sale', 'available', 45000000.00, 'total', NULL, NULL, 7200, 4, 'Off Forces Avenue, Old GRA, PH', 0),

('Luxury 5-Bedroom Detached House in Lekki', 'luxury-5-bedroom-detached-lekki',
 'Exquisite 5-bedroom detached mansion with a BQ, cinema room, private pool, and landscaped garden. Finishing touches of the highest quality throughout.',
 'house', 'sale', 'available', 250000000.00, 'total', 5, 6, 5500, 1, 'Ocean View Estate, Lekki', 1),

('Mini Flat for Lease in Ikeja', 'mini-flat-for-lease-ikeja',
 'A clean and modern mini flat with 1 bedroom, kitchenette, and prepaid meter. Close to major roads and public transport.',
 'house', 'lease', 'available', 1200000.00, 'year', 1, 1, 600, 2, 'Off Allen Avenue, Ikeja', 0);