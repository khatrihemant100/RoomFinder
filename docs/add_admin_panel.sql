-- Admin Panel Database Setup
-- Run this SQL script to add admin functionality

USE roomfinder;

-- 1. Add created_at column to users table if it doesn't exist (for admin panel)
-- This will show a warning if column already exists, but won't break
ALTER TABLE users 
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER role;

-- 2. Add admin role and is_admin column to users table
ALTER TABLE users 
ADD COLUMN is_admin TINYINT(1) DEFAULT 0 AFTER is_verified;

-- 3. Create admin_settings table for admin preferences
CREATE TABLE IF NOT EXISTS admin_settings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  setting_key VARCHAR(100) NOT NULL,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Insert default admin settings
INSERT INTO admin_settings (setting_key, setting_value) VALUES
('site_name', 'RoomFinder'),
('site_email', 'admin@roomfinder.com'),
('items_per_page', '20'),
('auto_approve_properties', '0')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- 5. Create admin_logs table for tracking admin actions
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

-- 6. Add status column to properties for approval workflow
ALTER TABLE properties
ADD COLUMN is_approved TINYINT(1) DEFAULT 1 AFTER status;

-- 7. Create index for admin queries
CREATE INDEX idx_users_admin ON users(is_admin, role);
CREATE INDEX idx_properties_approved ON properties(is_approved, created_at DESC);

-- Note: To create an admin user, run:
-- UPDATE users SET is_admin = 1 WHERE email = 'your-admin-email@example.com';

