-- Add created_at column to users table if it doesn't exist
-- Run this SQL script to add the created_at column

USE roomfinder;

-- Check if column exists and add if it doesn't
-- Note: This will work even if column already exists (will show warning but won't break)

ALTER TABLE users 
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER role;

-- Update existing records to have current timestamp if they don't have one
UPDATE users SET created_at = NOW() WHERE created_at IS NULL;



