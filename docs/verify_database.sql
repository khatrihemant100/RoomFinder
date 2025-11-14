-- Database Verification Script
-- यो script ले inquiry email system को लागि सबै tables र columns check गर्छ

USE roomfinder;

-- 1. Check if all required tables exist
SELECT 'Checking Tables...' AS Status;

SELECT 
    TABLE_NAME AS 'Table Name',
    CASE 
        WHEN TABLE_NAME IN ('properties', 'users', 'inquiries') THEN '✅ Required'
        ELSE '⚠️ Optional'
    END AS 'Status'
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'roomfinder'
ORDER BY TABLE_NAME;

-- 2. Check properties table structure
SELECT 'Checking Properties Table...' AS Status;

SELECT 
    COLUMN_NAME AS 'Column',
    DATA_TYPE AS 'Type',
    IS_NULLABLE AS 'Nullable',
    COLUMN_DEFAULT AS 'Default'
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'roomfinder' 
AND TABLE_NAME = 'properties'
ORDER BY ORDINAL_POSITION;

-- 3. Check users table structure
SELECT 'Checking Users Table...' AS Status;

SELECT 
    COLUMN_NAME AS 'Column',
    DATA_TYPE AS 'Type',
    IS_NULLABLE AS 'Nullable',
    COLUMN_DEFAULT AS 'Default'
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'roomfinder' 
AND TABLE_NAME = 'users'
ORDER BY ORDINAL_POSITION;

-- 4. Check if inquiries table exists
SELECT 'Checking Inquiries Table...' AS Status;

SELECT 
    COLUMN_NAME AS 'Column',
    DATA_TYPE AS 'Type',
    IS_NULLABLE AS 'Nullable',
    COLUMN_DEFAULT AS 'Default'
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'roomfinder' 
AND TABLE_NAME = 'inquiries'
ORDER BY ORDINAL_POSITION;

-- 5. Check if properties have valid user_id (owner)
SELECT 'Checking Properties with Owner Emails...' AS Status;

SELECT 
    p.id AS 'Room ID',
    p.title AS 'Room Title',
    p.user_id AS 'Owner User ID',
    u.name AS 'Owner Name',
    u.email AS 'Owner Email',
    CASE 
        WHEN u.email IS NULL THEN '❌ Email Missing'
        WHEN u.email = '' THEN '❌ Email Empty'
        ELSE '✅ OK'
    END AS 'Status'
FROM properties p
LEFT JOIN users u ON p.user_id = u.id
ORDER BY p.id
LIMIT 10;

-- 6. Count total properties with/without owner emails
SELECT 'Summary Statistics...' AS Status;

SELECT 
    COUNT(*) AS 'Total Properties',
    COUNT(u.email) AS 'Properties with Owner Email',
    COUNT(*) - COUNT(u.email) AS 'Properties without Owner Email'
FROM properties p
LEFT JOIN users u ON p.user_id = u.id;

