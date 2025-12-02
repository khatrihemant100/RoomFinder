-- =====================================================
-- RoomFinder - Complete Database Setup Script
-- यो script ले complete database structure create गर्छ
-- Copy गरेर phpMyAdmin मा run गर्नुहोस्
-- =====================================================

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS roomfinder CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE roomfinder;

-- =====================================================
-- STEP 1: Drop all existing tables (clean slate)
-- =====================================================

DROP TABLE IF EXISTS admin_logs;
DROP TABLE IF EXISTS admin_settings;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS inquiries;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS rooms; -- Legacy table (if exists)

-- =====================================================
-- STEP 2: Create Users Table
-- =====================================================

CREATE TABLE users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  password VARCHAR(255) DEFAULT NULL,
  role ENUM('owner','seeker') DEFAULT 'seeker',
  profile_photo VARCHAR(255) DEFAULT NULL,
  is_verified TINYINT(1) DEFAULT 0,
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  KEY idx_users_admin (is_admin, role),
  KEY idx_users_verified (is_verified, role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STEP 3: Create Properties Table
-- =====================================================

CREATE TABLE properties (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) DEFAULT NULL,
  title VARCHAR(255) DEFAULT NULL,
  location VARCHAR(255) DEFAULT NULL,
  price DECIMAL(10,2) DEFAULT NULL,
  utilities_cost DECIMAL(10,2) DEFAULT 0.00,
  management_fee DECIMAL(10,2) DEFAULT 0.00,
  deposit DECIMAL(10,2) DEFAULT 0.00,
  key_money DECIMAL(10,2) DEFAULT 0.00,
  type VARCHAR(50) DEFAULT NULL,
  train_station VARCHAR(255) DEFAULT NULL,
  status VARCHAR(50) DEFAULT 'available',
  is_approved TINYINT(1) DEFAULT 1,
  description TEXT DEFAULT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY idx_properties_status (status),
  KEY idx_properties_location (location),
  KEY idx_properties_price (price),
  KEY idx_properties_approved (is_approved, created_at DESC),
  CONSTRAINT properties_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STEP 4: Create Inquiries Table
-- =====================================================

CREATE TABLE inquiries (
  id INT(11) NOT NULL AUTO_INCREMENT,
  property_id INT(11) DEFAULT NULL,
  name VARCHAR(100) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  visit_date DATE DEFAULT NULL,
  message TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY property_id (property_id),
  CONSTRAINT inquiries_ibfk_1 FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STEP 5: Create Messages Table
-- =====================================================

CREATE TABLE messages (
  id INT(11) NOT NULL AUTO_INCREMENT,
  sender_id INT(11) NOT NULL,
  receiver_id INT(11) NOT NULL,
  property_id INT(11) DEFAULT NULL,
  subject VARCHAR(255) DEFAULT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY sender_id (sender_id),
  KEY receiver_id (receiver_id),
  KEY property_id (property_id),
  KEY is_read (is_read),
  KEY idx_messages_conversation (sender_id, receiver_id, created_at DESC),
  KEY idx_messages_unread (receiver_id, is_read, created_at DESC),
  CONSTRAINT messages_ibfk_1 FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT messages_ibfk_2 FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT messages_ibfk_3 FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STEP 6: Create Contacts Table
-- =====================================================

CREATE TABLE contacts (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  message TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STEP 7: Create Admin Settings Table
-- =====================================================

CREATE TABLE admin_settings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  setting_key VARCHAR(100) NOT NULL,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin settings
INSERT INTO admin_settings (setting_key, setting_value) VALUES
('site_name', 'RoomFinder'),
('site_email', 'admin@roomfinder.com'),
('items_per_page', '20'),
('auto_approve_properties', '0')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- =====================================================
-- STEP 8: Create Admin Logs Table
-- =====================================================

CREATE TABLE admin_logs (
  id INT(11) NOT NULL AUTO_INCREMENT,
  admin_id INT(11) NOT NULL,
  action VARCHAR(100) NOT NULL,
  target_type VARCHAR(50) NOT NULL,
  target_id INT(11) DEFAULT NULL,
  details TEXT,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY admin_id (admin_id),
  KEY created_at (created_at),
  CONSTRAINT admin_logs_ibfk_1 FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- COMPLETE!
-- =====================================================
-- Database structure created successfully!
-- 
-- Next Steps:
-- 1. Create your first admin user:
--    INSERT INTO users (name, email, password, role, is_admin) 
--    VALUES ('Admin', 'admin@example.com', '$2y$10$YourHashedPassword', 'owner', 1);
--
-- 2. Or use the admin creation script:
--    Visit: http://localhost/RoomFinder/admin/create_admin.php
--
-- 3. Create test users and properties as needed
-- =====================================================

