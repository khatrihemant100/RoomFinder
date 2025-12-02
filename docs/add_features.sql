-- Add Missing Features: Verified Owners, Rent Calculator Support
-- Run this SQL script to add verification system

USE roomfinder;

-- 1. Add is_verified column to users table for verified owners
ALTER TABLE users 
ADD COLUMN is_verified TINYINT(1) DEFAULT 0 AFTER role;

-- 2. Add utility fields to properties table for rent calculator
ALTER TABLE properties
ADD COLUMN utilities_cost DECIMAL(10,2) DEFAULT 0.00 AFTER price,
ADD COLUMN management_fee DECIMAL(10,2) DEFAULT 0.00 AFTER utilities_cost,
ADD COLUMN deposit DECIMAL(10,2) DEFAULT 0.00 AFTER management_fee,
ADD COLUMN key_money DECIMAL(10,2) DEFAULT 0.00 AFTER deposit;

-- 3. Create index for verified users
CREATE INDEX idx_users_verified ON users(is_verified, role);

