-- Fix Role ENUM in Users Table
-- यदि role column मा problem छ भने, यो script run गर्नुहोस्

USE roomfinder;

-- Check current role column structure
SHOW COLUMNS FROM users LIKE 'role';

-- Option 1: If role column is ENUM, verify it has correct values
-- यदि ENUM छ भने, यो check गर्नुहोस्:
SELECT COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'roomfinder' 
AND TABLE_NAME = 'users' 
AND COLUMN_NAME = 'role';

-- Option 2: If role column needs to be fixed, use this:
-- (यो run गर्नु अघि, सबै users को role check गर्नुहोस्)
ALTER TABLE users 
MODIFY COLUMN role ENUM('owner','seeker') DEFAULT 'seeker' NOT NULL;

-- Option 3: Check existing roles in database
SELECT role, COUNT(*) as count 
FROM users 
GROUP BY role;

-- Option 4: Fix any invalid roles (यदि invalid role छ भने)
-- UPDATE users SET role = 'seeker' WHERE role NOT IN ('owner', 'seeker');

