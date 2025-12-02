-- =====================================================
-- RoomFinder Database Cleanup and Rename Script
-- यो script ले database लाई clean र clear बनाउँछ
-- =====================================================

USE roomfinder;

-- =====================================================
-- STEP 1: Delete Unused Tables
-- =====================================================

-- Delete legacy 'rooms' table (not used, replaced by 'properties')
DROP TABLE IF EXISTS rooms;

-- =====================================================
-- STEP 2: Rename Columns for Better Clarity
-- =====================================================

-- Rename inquiries.room_id to inquiries.property_id (clearer name)
-- First, drop foreign key constraint
ALTER TABLE inquiries 
DROP FOREIGN KEY IF EXISTS inquiries_ibfk_1;

-- Rename the column
ALTER TABLE inquiries 
CHANGE COLUMN room_id property_id INT(11) DEFAULT NULL;

-- Re-add foreign key with new column name
ALTER TABLE inquiries
ADD CONSTRAINT inquiries_ibfk_1 
FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE;

-- Rename messages.room_id to messages.property_id (clearer name)
-- First, drop foreign key constraint
ALTER TABLE messages 
DROP FOREIGN KEY IF EXISTS messages_ibfk_3;

-- Rename the column
ALTER TABLE messages 
CHANGE COLUMN room_id property_id INT(11) DEFAULT NULL;

-- Re-add foreign key with new column name
ALTER TABLE messages
ADD CONSTRAINT messages_ibfk_3 
FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL;

-- =====================================================
-- STEP 3: Ensure All Required Columns Exist
-- =====================================================

-- Users table columns
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(255) DEFAULT NULL AFTER role,
ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0 AFTER profile_photo,
ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) DEFAULT 0 AFTER is_verified,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER is_admin;

-- Properties table columns
ALTER TABLE properties
ADD COLUMN IF NOT EXISTS train_station VARCHAR(255) DEFAULT NULL AFTER type,
ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'available' AFTER train_station,
ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1 AFTER status,
ADD COLUMN IF NOT EXISTS utilities_cost DECIMAL(10,2) DEFAULT 0.00 AFTER price,
ADD COLUMN IF NOT EXISTS management_fee DECIMAL(10,2) DEFAULT 0.00 AFTER utilities_cost,
ADD COLUMN IF NOT EXISTS deposit DECIMAL(10,2) DEFAULT 0.00 AFTER management_fee,
ADD COLUMN IF NOT EXISTS key_money DECIMAL(10,2) DEFAULT 0.00 AFTER deposit;

-- =====================================================
-- STEP 4: Create Missing Tables (if they don't exist)
-- =====================================================

-- Create messages table if it doesn't exist
CREATE TABLE IF NOT EXISTS messages (
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
  CONSTRAINT messages_ibfk_1 FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT messages_ibfk_2 FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT messages_ibfk_3 FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create admin_settings table if it doesn't exist
CREATE TABLE IF NOT EXISTS admin_settings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  setting_key VARCHAR(100) NOT NULL,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin settings
INSERT INTO admin_settings (setting_key, setting_value) VALUES
('site_name', 'RoomFinder'),
('site_email', 'admin@roomfinder.com'),
('items_per_page', '20'),
('auto_approve_properties', '0')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Create admin_logs table if it doesn't exist
CREATE TABLE IF NOT EXISTS admin_logs (
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
  FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- STEP 5: Create/Update Indexes for Performance
-- =====================================================

-- Messages indexes
CREATE INDEX IF NOT EXISTS idx_messages_conversation ON messages(sender_id, receiver_id, created_at DESC);
CREATE INDEX IF NOT EXISTS idx_messages_unread ON messages(receiver_id, is_read, created_at DESC);

-- Users indexes
CREATE INDEX IF NOT EXISTS idx_users_admin ON users(is_admin, role);
CREATE INDEX IF NOT EXISTS idx_users_verified ON users(is_verified, role);

-- Properties indexes
CREATE INDEX IF NOT EXISTS idx_properties_status ON properties(status);
CREATE INDEX IF NOT EXISTS idx_properties_location ON properties(location);
CREATE INDEX IF NOT EXISTS idx_properties_price ON properties(price);
CREATE INDEX IF NOT EXISTS idx_properties_approved ON properties(is_approved, created_at DESC);

-- =====================================================
-- STEP 6: Update Existing Data (if needed)
-- =====================================================

-- Update users.created_at for existing records
UPDATE users SET created_at = NOW() WHERE created_at IS NULL;

-- =====================================================
-- VERIFICATION QUERIES (Run these to verify)
-- =====================================================

-- Check all tables
-- SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'roomfinder';

-- Check inquiries table structure
-- SHOW COLUMNS FROM inquiries;

-- Check messages table structure  
-- SHOW COLUMNS FROM messages;

-- Check users table structure
-- SHOW COLUMNS FROM users;

-- Check properties table structure
-- SHOW COLUMNS FROM properties;

